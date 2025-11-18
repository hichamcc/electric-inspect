<?php

use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard/chart-data', [App\Http\Controllers\DashboardController::class, 'chartData'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.chart-data');

Route::get('dashboard', function () {
    $user = auth()->user();

    if ($user->isSuperAdmin()) {
        // Super Admin Dashboard - Show organization stats
        $totalOrganizations = \App\Models\Organization::count();
        $activeOrganizations = \App\Models\Organization::where('is_active', true)->count();
        $totalUsers = \App\Models\User::where('role', '!=', 'super_admin')->count();
        $totalInspections = \App\Models\Inspection::count();
        $organizations = \App\Models\Organization::withCount(['users', 'inspections'])
            ->latest()
            ->paginate(10);

        return view('dashboard-super-admin', compact(
            'totalOrganizations',
            'activeOrganizations',
            'totalUsers',
            'totalInspections',
            'organizations'
        ));
    } elseif ($user->isTechnician()) {
        // Technicians see their stats and upcoming inspections
        $stats = [
            'total' => \App\Models\Inspection::where('inspector_id', $user->id)->count(),
            'completed' => \App\Models\Inspection::where('inspector_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'in_progress' => \App\Models\Inspection::where('inspector_id', $user->id)
                ->where('status', 'in_progress')
                ->count(),
            'upcoming' => \App\Models\Inspection::where('inspector_id', $user->id)
                ->where('inspection_date', '>=', now())
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->count(),
        ];

        $upcomingInspections = \App\Models\Inspection::with(['customer', 'equipment'])
            ->where('inspector_id', $user->id)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('inspection_date')
            ->orderBy('inspection_time')
            ->get();

        return view('dashboard', compact('stats', 'upcomingInspections'));
    } else {
        // Organization admins see all organization inspections
        $stats = [
            'customers' => \App\Models\Customer::where('organization_id', $user->organization_id)->count(),
            'equipment' => \App\Models\Equipment::where('organization_id', $user->organization_id)->count(),
            'inspections' => \App\Models\Inspection::where('organization_id', $user->organization_id)->count(),
            'upcoming' => \App\Models\Inspection::where('organization_id', $user->organization_id)
                ->where('inspection_date', '>', now())
                ->where('status', 'scheduled')
                ->count(),
        ];
        return view('dashboard', compact('stats'));
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'tenant'])->group(function () {
    // Notification routes
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread', [App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Customer routes
    Route::resource('customers', App\Http\Controllers\CustomerController::class);

    // Equipment routes
    Route::resource('equipment', App\Http\Controllers\EquipmentController::class);

    // Equipment Type routes (only for admins)
    Route::resource('equipment-types', App\Http\Controllers\EquipmentTypeController::class);

    // Inspection routes
    Route::get('inspections-calendar', [App\Http\Controllers\InspectionController::class, 'calendar'])
        ->name('inspections.calendar');
    Route::get('inspections-calendar/data', [App\Http\Controllers\InspectionController::class, 'calendarData'])
        ->name('inspections.calendar.data');
    Route::resource('inspections', App\Http\Controllers\InspectionController::class);
    Route::delete('inspections/{inspection}/files/{file}', [App\Http\Controllers\InspectionController::class, 'deleteFile'])
        ->name('inspections.files.delete');

    // Inspection export routes
    Route::get('inspections/{inspection}/export-pdf', [App\Http\Controllers\InspectionController::class, 'exportPdf'])
        ->name('inspections.export.pdf');
    Route::get('inspections/{inspection}/preview-pdf', [App\Http\Controllers\InspectionController::class, 'previewPdf'])
        ->name('inspections.preview.pdf');
    Route::get('inspections-export/excel', [App\Http\Controllers\InspectionController::class, 'exportExcel'])
        ->name('inspections.export.excel');
    Route::get('inspections-export/summary-pdf', [App\Http\Controllers\InspectionController::class, 'exportSummaryPdf'])
        ->name('inspections.export.summary.pdf');

    // User/Technician management routes (only for admins)
    Route::resource('users', App\Http\Controllers\UserController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');

    // Organization settings (only for admins)
    Route::get('settings/organization', [Settings\OrganizationController::class, 'edit'])->name('settings.organization.edit');
    Route::put('settings/organization', [Settings\OrganizationController::class, 'update'])->name('settings.organization.update');
    Route::delete('settings/organization/logo', [Settings\OrganizationController::class, 'deleteLogo'])->name('settings.organization.logo.delete');
});

// Super Admin routes
Route::middleware(['auth'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::resource('organizations', App\Http\Controllers\SuperAdmin\OrganizationController::class);
    Route::get('organizations/{organization}/admins/create', [App\Http\Controllers\SuperAdmin\OrganizationController::class, 'createAdmin'])->name('organizations.admins.create');
    Route::post('organizations/{organization}/admins', [App\Http\Controllers\SuperAdmin\OrganizationController::class, 'storeAdmin'])->name('organizations.admins.store');
});

require __DIR__.'/auth.php';
