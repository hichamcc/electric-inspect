<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations.
     */
    public function index(Request $request)
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $query = Organization::withCount(['users', 'inspections']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $organizations = $query->latest()->paginate(15);

        return view('super-admin.organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('super-admin.organizations.create');
    }

    /**
     * Store a newly created organization in storage.
     */
    public function store(Request $request)
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
            // Admin user details
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        // Create organization
        $organization = Organization::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Create initial admin user for this organization
        User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'organization_id' => $organization->id,
            'role' => 'organization_admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('super-admin.organizations.show', $organization)
            ->with('success', 'Organization and admin account created successfully.');
    }

    /**
     * Display the specified organization.
     */
    public function show(Organization $organization)
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $organization->load(['users', 'customers', 'equipment', 'inspections']);

        $stats = [
            'total_users' => $organization->users()->count(),
            'admins' => $organization->users()->where('role', 'organization_admin')->count(),
            'technicians' => $organization->users()->where('role', 'technician')->count(),
            'customers' => $organization->customers()->count(),
            'equipment' => $organization->equipment()->count(),
            'total_inspections' => $organization->inspections()->count(),
            'completed_inspections' => $organization->inspections()->where('status', 'completed')->count(),
            'scheduled_inspections' => $organization->inspections()->where('status', 'scheduled')->count(),
        ];

        $admins = $organization->users()->where('role', 'organization_admin')->get();

        return view('super-admin.organizations.show', compact('organization', 'stats', 'admins'));
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit(Organization $organization)
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('super-admin.organizations.edit', compact('organization'));
    }

    /**
     * Update the specified organization in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug,' . $organization->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $organization->update($validated);

        return redirect()->route('super-admin.organizations.show', $organization)
            ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified organization from storage.
     */
    public function destroy(Organization $organization)
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Soft delete by deactivating instead of hard delete
        $organization->update(['is_active' => false]);

        return redirect()->route('super-admin.organizations.index')
            ->with('success', 'Organization deactivated successfully.');
    }

    /**
     * Create a new admin user for the organization
     */
    public function createAdmin(Organization $organization)
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('super-admin.organizations.create-admin', compact('organization'));
    }

    /**
     * Store a new admin user for the organization
     */
    public function storeAdmin(Request $request, Organization $organization)
    {
        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $organization->id,
            'role' => 'organization_admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('super-admin.organizations.show', $organization)
            ->with('success', 'Admin user created successfully.');
    }
}
