<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepedEmailController;
use App\Http\Controllers\EmailConcernController;
use App\Http\Controllers\IctMaintenanceController;
use App\Http\Controllers\InspectionFormController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UnratedCheckController;
use App\Http\Controllers\ViewEmailConcernController;
use App\Http\Controllers\ViewIctInspectionController;
use App\Http\Controllers\ViewIctMaintenanceController;
use App\Http\Controllers\ViewSoftwareRequestController;
use Illuminate\Support\Facades\Route;

// Guest: redirect root to login
Route::get('/', fn () => redirect('/login'));

// Employee routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API endpoints
    Route::get('/api/calendar-events', [CalendarEventController::class, 'index']);
    Route::get('/api/check-unrated', [UnratedCheckController::class, 'check']);
    Route::post('/api/update-rated/{requestId}', [RatingController::class, 'updateRated']);

    // Request Forms
    Route::get('/ict-maintenance', [IctMaintenanceController::class, 'create'])->name('ict-maintenance.create');
    Route::post('/ict-maintenance', [IctMaintenanceController::class, 'store'])->name('ict-maintenance.store');

    Route::get('/inspection-form', [InspectionFormController::class, 'create'])->name('inspection-form.create');
    Route::post('/inspection-form', [InspectionFormController::class, 'store'])->name('inspection-form.store');

    Route::get('/deped-email', [DepedEmailController::class, 'create'])->name('deped-email.create');
    Route::post('/deped-email', [DepedEmailController::class, 'store'])->name('deped-email.store');

    Route::get('/email-concern', [EmailConcernController::class, 'create'])->name('email-concern.create');
    Route::post('/email-concern', [EmailConcernController::class, 'store'])->name('email-concern.store');

    // Status
    Route::get('/status', [StatusController::class, 'index'])->name('status');

    // View/Edit pages
    Route::get('/status/view/ict-maintenance/{requestId}', [ViewIctMaintenanceController::class, 'show']);
    Route::post('/status/view/ict-maintenance/{requestId}', [ViewIctMaintenanceController::class, 'update']);

    Route::get('/status/view/ict-inspection/{requestId}', [ViewIctInspectionController::class, 'show']);
    Route::post('/status/view/ict-inspection/{requestId}', [ViewIctInspectionController::class, 'update']);

    Route::get('/status/view/email-concern/{requestId}', [ViewEmailConcernController::class, 'show']);
    Route::post('/status/view/email-concern/{requestId}', [ViewEmailConcernController::class, 'update']);

    Route::get('/status/view/software-request/{requestId}', [ViewSoftwareRequestController::class, 'show']);
    Route::post('/status/view/software-request/{requestId}', [ViewSoftwareRequestController::class, 'update']);
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/requests', [AdminRequestController::class, 'index'])->name('admin.requests');
    Route::patch('/requests/{id}', [AdminRequestController::class, 'update'])->name('admin.requests.update');
});
