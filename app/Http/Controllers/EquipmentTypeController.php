<?php

namespace App\Http\Controllers;

use App\Models\EquipmentType;
use App\Models\EquipmentTypeParameter;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Only organization admins can access
        if (auth()->user()->isTechnician()) {
            abort(403, 'Unauthorized action.');
        }

        $query = EquipmentType::with('parameters');

        // Filter by organization
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('organization_id', auth()->user()->organization_id);
        }

        $equipmentTypes = $query->orderBy('name')->paginate(15);

        return view('equipment-types.index', compact('equipmentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only organization admins can access
        if (auth()->user()->isTechnician()) {
            abort(403, 'Unauthorized action.');
        }

        return view('equipment-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only organization admins can access
        if (auth()->user()->isTechnician()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parameters' => 'nullable|array',
            'parameters.*.label' => 'required|string|max:255',
            'parameters.*.is_required' => 'nullable|boolean',
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;

        $equipmentType = EquipmentType::create([
            'organization_id' => $validated['organization_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Create parameters if any
        if (!empty($validated['parameters'])) {
            foreach ($validated['parameters'] as $index => $parameter) {
                // Auto-generate name from label
                $name = \Illuminate\Support\Str::slug($parameter['label'], '_');

                EquipmentTypeParameter::create([
                    'equipment_type_id' => $equipmentType->id,
                    'name' => $name,
                    'label' => $parameter['label'],
                    'is_required' => $parameter['is_required'] ?? false,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('equipment-types.show', $equipmentType)
            ->with('success', 'Equipment type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EquipmentType $equipmentType)
    {
        // Only organization admins can access
        if (auth()->user()->isTechnician()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure user can only view their organization's equipment types
        if (!auth()->user()->isSuperAdmin() && $equipmentType->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        $equipmentType->load('parameters', 'equipment');

        return view('equipment-types.show', compact('equipmentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipmentType $equipmentType)
    {
        // Only organization admins can access
        if (auth()->user()->isTechnician()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure user can only edit their organization's equipment types
        if (!auth()->user()->isSuperAdmin() && $equipmentType->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        $equipmentType->load('parameters');

        return view('equipment-types.edit', compact('equipmentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EquipmentType $equipmentType)
    {
        // Only organization admins can access
        if (auth()->user()->isTechnician()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure user can only update their organization's equipment types
        if (!auth()->user()->isSuperAdmin() && $equipmentType->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parameters' => 'nullable|array',
            'parameters.*.label' => 'required|string|max:255',
            'parameters.*.is_required' => 'nullable|boolean',
        ]);

        $equipmentType->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Delete existing parameters and recreate
        $equipmentType->parameters()->delete();

        // Create parameters if any
        if (!empty($validated['parameters'])) {
            foreach ($validated['parameters'] as $index => $parameter) {
                // Auto-generate name from label
                $name = \Illuminate\Support\Str::slug($parameter['label'], '_');

                EquipmentTypeParameter::create([
                    'equipment_type_id' => $equipmentType->id,
                    'name' => $name,
                    'label' => $parameter['label'],
                    'is_required' => $parameter['is_required'] ?? false,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('equipment-types.show', $equipmentType)
            ->with('success', 'Equipment type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquipmentType $equipmentType)
    {
        // Only organization admins can access
        if (auth()->user()->isTechnician()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure user can only delete their organization's equipment types
        if (!auth()->user()->isSuperAdmin() && $equipmentType->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if any equipment uses this type
        if ($equipmentType->equipment()->count() > 0) {
            return redirect()->route('equipment-types.index')
                ->with('error', 'Cannot delete equipment type that is assigned to equipment.');
        }

        $equipmentType->delete();

        return redirect()->route('equipment-types.index')
            ->with('success', 'Equipment type deleted successfully.');
    }
}
