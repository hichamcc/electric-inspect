<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipment::with(['customer', 'organization']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('equipment_type', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('manufacturer', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $equipment = $query->latest()->paginate(15);
        $customers = Customer::orderBy('company_name')->get();

        return view('equipment.index', compact('equipment', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('company_name')->get();
        $equipmentTypes = EquipmentType::where('organization_id', auth()->user()->organization_id)
            ->orderBy('name')
            ->get();
        return view('equipment.create', compact('customers', 'equipmentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'equipment_type' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'installation_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,maintenance,retired',
            'notes' => 'nullable|string',
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;

        $equipment = Equipment::create($validated);

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'Equipment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        $equipment->load(['customer', 'inspections.inspector']);

        return view('equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        $customers = Customer::orderBy('company_name')->get();
        $equipmentTypes = EquipmentType::where('organization_id', auth()->user()->organization_id)
            ->orderBy('name')
            ->get();
        return view('equipment.edit', compact('equipment', 'customers', 'equipmentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'equipment_type' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'installation_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,maintenance,retired',
            'notes' => 'nullable|string',
        ]);

        $equipment->update($validated);

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'Equipment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment deleted successfully.');
    }
}
