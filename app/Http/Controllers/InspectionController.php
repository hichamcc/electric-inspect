<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\InspectionFile;
use App\Models\InspectionParameterValue;
use App\Services\PdfExportService;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Inspection::with(['customer', 'equipment', 'inspector']);

        // Filter by inspector for technicians
        if (auth()->user()->isTechnician()) {
            $query->where('inspector_id', auth()->id());
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inspection_type', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('company_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('equipment', function ($q) use ($search) {
                      $q->where('equipment_type', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by result
        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $inspections = $query->latest('inspection_date')->paginate(15);
        $customers = Customer::orderBy('company_name')->get();

        return view('inspections.index', compact('inspections', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('organization_id', auth()->user()->organization_id)
            ->orderBy('company_name')
            ->get();

        $equipment = Equipment::with(['customer', 'equipmentType.parameters'])
            ->where('organization_id', auth()->user()->organization_id)
            ->orderBy('equipment_type')
            ->get();

        // Get technicians for assignment (only for admins)
        $technicians = null;
        if (!auth()->user()->isTechnician()) {
            $technicians = \App\Models\User::where('organization_id', auth()->user()->organization_id)
                ->where('role', 'technician')
                ->orderBy('name')
                ->get();
        }

        // Prepare equipment data for JavaScript
        $equipmentData = $equipment->mapWithKeys(function($item) {
            $parameters = [];
            if ($item->equipmentType && $item->equipmentType->parameters) {
                $parameters = $item->equipmentType->parameters->map(function($param) {
                    return [
                        'id' => $param->id,
                        'name' => $param->name,
                        'label' => $param->label,
                        'is_required' => $param->is_required,
                    ];
                })->values()->toArray();
            }

            return [
                $item->id => [
                    'customer_id' => $item->customer_id,
                    'equipment_type' => $item->equipment_type,
                    'equipment_type_id' => $item->equipment_type_id,
                    'parameters' => $parameters
                ]
            ];
        });

        return view('inspections.create', compact('customers', 'technicians', 'equipmentData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_id' => 'required|exists:equipment,id',
            'inspection_type' => 'nullable|string|max:255',
            'inspection_date' => 'required|date',
            'inspection_time' => 'nullable',
            'status' => 'required|in:scheduled,in_progress,cancelled',
            'notes' => 'nullable|string',
            'inspector_id' => 'nullable|exists:users,id',
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;
        $validated['result'] = 'Pending'; // Default result

        // Assign inspector: use selected technician for admins, current user for technicians
        if (auth()->user()->isTechnician() || !$request->filled('inspector_id')) {
            $validated['inspector_id'] = auth()->id();
        }

        $inspection = Inspection::create($validated);

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Inspection scheduled successfully. Use Edit to fill parameters and results.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inspection $inspection)
    {
        $inspection->load(['customer', 'equipment.equipmentType', 'inspector', 'files', 'parameterValues.parameter']);

        return view('inspections.show', compact('inspection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inspection $inspection)
    {
        $customers = Customer::where('organization_id', auth()->user()->organization_id)
            ->orderBy('company_name')
            ->get();

        $equipment = Equipment::with(['customer', 'equipmentType.parameters'])
            ->where('organization_id', auth()->user()->organization_id)
            ->orderBy('equipment_type')
            ->get();

        // Load inspection with parameter values
        $inspection->load(['parameterValues']);

        // Get technicians for assignment (only for admins)
        $technicians = null;
        if (!auth()->user()->isTechnician()) {
            $technicians = \App\Models\User::where('organization_id', auth()->user()->organization_id)
                ->where('role', 'technician')
                ->orderBy('name')
                ->get();
        }

        // Prepare equipment data for JavaScript
        $equipmentData = $equipment->mapWithKeys(function($item) {
            $parameters = [];
            if ($item->equipmentType && $item->equipmentType->parameters) {
                $parameters = $item->equipmentType->parameters->map(function($param) {
                    return [
                        'id' => $param->id,
                        'name' => $param->name,
                        'label' => $param->label,
                        'is_required' => $param->is_required,
                    ];
                })->values()->toArray();
            }

            return [
                $item->id => [
                    'customer_id' => $item->customer_id,
                    'equipment_type' => $item->equipment_type,
                    'equipment_type_id' => $item->equipment_type_id,
                    'parameters' => $parameters
                ]
            ];
        });

        return view('inspections.edit', compact('inspection', 'customers', 'technicians', 'equipmentData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inspection $inspection)
    {
        // Build validation rules based on user role
        $rules = [
            'inspection_type' => 'nullable|string|max:255',
            'result' => 'required|string|max:255',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'inspector_id' => 'nullable|exists:users,id',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'parameters' => 'nullable|array',
            'parameters.*' => 'nullable|string',
        ];

        // Only admins can update these fields
        if (!auth()->user()->isTechnician()) {
            $rules['customer_id'] = 'required|exists:customers,id';
            $rules['equipment_id'] = 'required|exists:equipment,id';
            $rules['inspection_date'] = 'required|date';
            $rules['inspection_time'] = 'nullable';
        }

        $validated = $request->validate($rules);

        // Handle inspector assignment for admins
        if (!auth()->user()->isTechnician() && $request->filled('inspector_id')) {
            // Admins can reassign
            $validated['inspector_id'] = $request->inspector_id;
        } else {
            // Technicians keep their assignment, don't change inspector_id
            unset($validated['inspector_id']);
        }

        // Prevent technicians from changing customer or equipment
        if (auth()->user()->isTechnician()) {
            // Technicians cannot change customer or equipment
            unset($validated['customer_id']);
            unset($validated['equipment_id']);
            unset($validated['inspection_date']);
            unset($validated['inspection_time']);
        }

        $inspection->update($validated);

        // Update parameter values - delete old and create new
        $inspection->parameterValues()->delete();
        if (!empty($validated['parameters'])) {
            foreach ($validated['parameters'] as $parameterId => $value) {
                if ($value !== null && $value !== '') {
                    InspectionParameterValue::create([
                        'inspection_id' => $inspection->id,
                        'equipment_type_parameter_id' => $parameterId,
                        'value' => $value,
                    ]);
                }
            }
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('inspections', 'public');

                InspectionFile::create([
                    'fileable_id' => $inspection->id,
                    'fileable_type' => Inspection::class,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Inspection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inspection $inspection)
    {
        // Delete associated files
        foreach ($inspection->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $inspection->delete();

        return redirect()->route('inspections.index')
            ->with('success', 'Inspection deleted successfully.');
    }

    /**
     * Delete a specific file from an inspection.
     */
    public function deleteFile(Inspection $inspection, InspectionFile $file)
    {
        // Ensure the file belongs to the inspection
        if ($file->fileable_id !== $inspection->id) {
            abort(404);
        }

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'File deleted successfully.');
    }

    /**
     * Export single inspection report as PDF
     */
    public function exportPdf(Inspection $inspection, PdfExportService $pdfService)
    {
        return $pdfService->downloadInspectionReport($inspection);
    }

    /**
     * Preview inspection report as PDF
     */
    public function previewPdf(Inspection $inspection, PdfExportService $pdfService)
    {
        return $pdfService->streamInspectionReport($inspection);
    }

    /**
     * Export inspections to Excel
     */
    public function exportExcel(Request $request, ExcelExportService $excelService)
    {
        $query = Inspection::with(['customer', 'equipment', 'inspector']);

        // Filter by inspector for technicians
        if (auth()->user()->isTechnician()) {
            $query->where('inspector_id', auth()->id());
        }

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inspection_type', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('company_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('equipment', function ($q) use ($search) {
                      $q->where('equipment_type', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $filters = $request->only(['search', 'status', 'result', 'customer_id']);

        return $excelService->exportInspections($query, $filters);
    }

    /**
     * Export inspections summary as PDF
     */
    public function exportSummaryPdf(Request $request, PdfExportService $pdfService)
    {
        $query = Inspection::with(['customer', 'equipment', 'inspector']);

        // Filter by inspector for technicians
        if (auth()->user()->isTechnician()) {
            $query->where('inspector_id', auth()->id());
        }

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inspection_type', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('company_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('equipment', function ($q) use ($search) {
                      $q->where('equipment_type', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $inspections = $query->latest('inspection_date')->get();
        $filters = $request->only(['search', 'status', 'result', 'customer_id']);

        return $pdfService->downloadSummaryReport($inspections, $filters);
    }

    /**
     * Show the calendar view
     */
    public function calendar()
    {
        return view('inspections.calendar');
    }

    /**
     * Get calendar data in JSON format for FullCalendar
     */
    public function calendarData(Request $request)
    {
        $query = Inspection::with(['customer', 'equipment', 'inspector']);

        // Filter by inspector for technicians
        if (auth()->user()->isTechnician()) {
            $query->where('inspector_id', auth()->id());
        } elseif (!auth()->user()->isSuperAdmin()) {
            // Filter by organization for non-super-admin users
            $query->where('organization_id', auth()->user()->organization_id);
        }

        // Get inspections within the calendar view range (with some padding)
        if ($request->has('start') && $request->has('end')) {
            // Clean the date strings - remove timezone info and parse properly
            $startDate = substr($request->start, 0, 10); // Get YYYY-MM-DD part
            $endDate = substr($request->end, 0, 10);     // Get YYYY-MM-DD part

            // Add padding of 1 month on each side
            $start = date('Y-m-d', strtotime($startDate . ' -1 month'));
            $end = date('Y-m-d', strtotime($endDate . ' +1 month'));

            $query->whereBetween('inspection_date', [$start, $end]);
        }

        $inspections = $query->get();

        \Log::info('Calendar data request', [
            'count' => $inspections->count(),
            'user' => auth()->id(),
            'role' => auth()->user()->role,
            'org_id' => auth()->user()->organization_id,
            'start_raw' => $request->start,
            'end_raw' => $request->end,
            'start_parsed' => $start ?? 'N/A',
            'end_parsed' => $end ?? 'N/A',
        ]);

        // Format events for FullCalendar
        $events = $inspections->map(function ($inspection) {
            // Color based on status - matching inspections index
            $colors = [
                'scheduled' => '#6b7280',      // Gray
                'completed' => '#10b981',      // Green
                'in_progress' => '#3b82f6',    // Blue
                'cancelled' => '#ef4444',      // Red
            ];

            $color = $colors[$inspection->status] ?? '#9ca3af'; // Light gray default

            // Format date properly - extract just the date part
            $dateOnly = date('Y-m-d', strtotime($inspection->inspection_date));
            $datetime = $dateOnly . 'T' . $inspection->inspection_time;

            return [
                'id' => $inspection->id,
                'title' => $inspection->inspection_type . ' - ' . $inspection->customer->company_name,
                'start' => $datetime,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('inspections.show', $inspection),
                'extendedProps' => [
                    'status' => $inspection->status,
                    'tooltip' => $inspection->inspection_type . "\n" .
                                $inspection->customer->company_name . "\n" .
                                $inspection->equipment->equipment_type . "\n" .
                                'Inspector: ' . $inspection->inspector->name . "\n" .
                                'Status: ' . ucfirst($inspection->status),
                ],
            ];
        });

        return response()->json($events);
    }
}
