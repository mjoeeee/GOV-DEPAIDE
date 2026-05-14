<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\AudioVisualEditingController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepedEmailController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\EmailConcernController;
use App\Http\Controllers\EmailManagementController;
use App\Http\Controllers\IctMaintenanceController;
use App\Http\Controllers\IctMaintenanceInspectionController;
use App\Http\Controllers\IdCardPrintingController;
use App\Http\Controllers\InspectionFormController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SoftwareDevelopmentController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UnratedCheckController;
use App\Http\Controllers\ViewAudioVisualEditingController;
use App\Http\Controllers\ViewDocumentationController;
use App\Http\Controllers\ViewEmailConcernController;
use App\Http\Controllers\ViewEmailManagementController;
use App\Http\Controllers\ViewIctInspectionController;
use App\Http\Controllers\ViewIctMaintenanceController;
use App\Http\Controllers\ViewIdCardPrintingController;
use App\Http\Controllers\ViewSoftwareRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('app.base_path'))->group(function () {
    // Guest: redirect root to login
    Route::get('/', fn () => redirect()->route('login'))->name('home');

    // Employee routes
    Route::middleware(['auth'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // API endpoints
        Route::get('/api/calendar-events', [CalendarEventController::class, 'index'])->name('api.calendar-events.index');
        Route::get('/api/check-unrated', [UnratedCheckController::class, 'check'])->name('api.check-unrated');
        Route::post('/api/update-rated/{requestId}', [RatingController::class, 'updateRated'])->name('api.update-rated');

        // Request Forms
        Route::get('/ict-maintenance-inspection', [IctMaintenanceInspectionController::class, 'create'])->name('ict-maintenance-inspection.create');
        Route::post('/ict-maintenance-inspection', [IctMaintenanceInspectionController::class, 'store'])->name('ict-maintenance-inspection.store');

        // Backward compatibility routes
        Route::get('/ict-maintenance', [IctMaintenanceController::class, 'create'])->name('ict-maintenance.create');
        Route::post('/ict-maintenance', [IctMaintenanceController::class, 'store'])->name('ict-maintenance.store');

        Route::get('/inspection-form', [InspectionFormController::class, 'create'])->name('inspection-form.create');
        Route::post('/inspection-form', [InspectionFormController::class, 'store'])->name('inspection-form.store');

        Route::get('/email-management', [EmailManagementController::class, 'create'])->name('email-management.create');
        Route::post('/email-management', [EmailManagementController::class, 'store'])->name('email-management.store');
        Route::get('/deped-email', [DepedEmailController::class, 'create'])->name('deped-email.create');
        Route::post('/deped-email', [DepedEmailController::class, 'store'])->name('deped-email.store');
        Route::get('/email-concern', [EmailConcernController::class, 'create'])->name('email-concern.create');
        Route::post('/email-concern', [EmailConcernController::class, 'store'])->name('email-concern.store');

        Route::get('/documentation', [DocumentationController::class, 'create'])->name('documentation.create');
        Route::post('/documentation', [DocumentationController::class, 'store'])->name('documentation.store');

        Route::get('/audio-visual', [AudioVisualEditingController::class, 'create'])->name('audio-visual.create');
        Route::post('/audio-visual', [AudioVisualEditingController::class, 'store'])->name('audio-visual.store');

        Route::get('/software-request', [SoftwareDevelopmentController::class, 'create'])->name('software-request.create');
        Route::post('/software-request', [SoftwareDevelopmentController::class, 'store'])->name('software-request.store');

        Route::get('/id-card-printing', [IdCardPrintingController::class, 'create'])->name('id-card-printing.create');
        Route::post('/id-card-printing', [IdCardPrintingController::class, 'store'])->name('id-card-printing.store');

        // Status
        Route::get('/status', [StatusController::class, 'index'])->name('status');

        // View/Edit pages
        Route::get('/status/view/ict-maintenance/{requestId}', [ViewIctMaintenanceController::class, 'show'])->name('status.view.ict-maintenance');
        Route::post('/status/view/ict-maintenance/{requestId}', [ViewIctMaintenanceController::class, 'update'])->name('status.update.ict-maintenance');

        Route::get('/status/view/ict-inspection/{requestId}', [ViewIctInspectionController::class, 'show'])->name('status.view.ict-inspection');
        Route::post('/status/view/ict-inspection/{requestId}', [ViewIctInspectionController::class, 'update'])->name('status.update.ict-inspection');

        Route::get('/status/view/email-management/{requestId}', [ViewEmailManagementController::class, 'show'])->name('status.view.email-management');
        Route::post('/status/view/email-management/{requestId}', [ViewEmailManagementController::class, 'update'])->name('status.update.email-management');
        Route::get('/status/view/email-concern/{requestId}', [ViewEmailConcernController::class, 'show'])->name('status.view.email-concern');
        Route::post('/status/view/email-concern/{requestId}', [ViewEmailConcernController::class, 'update'])->name('status.update.email-concern');

        Route::get('/status/view/software-request/{requestId}', [ViewSoftwareRequestController::class, 'show'])->name('status.view.software-request');
        Route::post('/status/view/software-request/{requestId}', [ViewSoftwareRequestController::class, 'update'])->name('status.update.software-request');

        Route::get('/status/view/documentation/{requestId}', [ViewDocumentationController::class, 'show'])->name('status.view.documentation');
        Route::post('/status/view/documentation/{requestId}', [ViewDocumentationController::class, 'update'])->name('status.update.documentation');

        Route::get('/status/view/audio-visual-editing/{requestId}', [ViewAudioVisualEditingController::class, 'show'])->name('status.view.audio-visual-editing');
        Route::post('/status/view/audio-visual-editing/{requestId}', [ViewAudioVisualEditingController::class, 'update'])->name('status.update.audio-visual-editing');

        Route::get('/status/view/id-card-printing/{requestId}', [ViewIdCardPrintingController::class, 'show'])->name('status.view.id-card-printing');
        Route::post('/status/view/id-card-printing/{requestId}', [ViewIdCardPrintingController::class, 'update'])->name('status.update.id-card-printing');

        // Backward compatibility routes for old email request types
        Route::get('/status/view/deped-email-request/{requestId}', [ViewEmailManagementController::class, 'show'])->name('status.view.deped-email-request');
        Route::post('/status/view/deped-email-request/{requestId}', [ViewEmailManagementController::class, 'update'])->name('status.update.deped-email-request');

        Route::get('/status/view/password-reset/{requestId}', [ViewEmailManagementController::class, 'show'])->name('status.view.password-reset');
        Route::post('/status/view/password-reset/{requestId}', [ViewEmailManagementController::class, 'update'])->name('status.update.password-reset');
    });

    // Admin routes
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/requests', [AdminRequestController::class, 'index'])->name('admin.requests');
        Route::patch('/requests/{id}', [AdminRequestController::class, 'update'])->name('admin.requests.update');
        Route::delete('/requests/{id}', [AdminRequestController::class, 'destroy'])->name('admin.requests.destroy');

        // Admin view routes (no user filter)
        Route::get('/view/ict-maintenance/{requestId}', [ViewIctMaintenanceController::class, 'show'])->name('admin.view.ict-maintenance');
        Route::post('/view/ict-maintenance/{requestId}', [ViewIctMaintenanceController::class, 'update'])->name('admin.update.ict-maintenance');

        Route::get('/view/ict-inspection/{requestId}', [ViewIctInspectionController::class, 'show'])->name('admin.view.ict-inspection');
        Route::post('/view/ict-inspection/{requestId}', [ViewIctInspectionController::class, 'update'])->name('admin.update.ict-inspection');

        Route::get('/view/email-management/{requestId}', [ViewEmailManagementController::class, 'show'])->name('admin.view.email-management');
        Route::post('/view/email-management/{requestId}', [ViewEmailManagementController::class, 'update'])->name('admin.update.email-management');
        Route::get('/view/email-concern/{requestId}', [ViewEmailConcernController::class, 'show'])->name('admin.view.email-concern');
        Route::post('/view/email-concern/{requestId}', [ViewEmailConcernController::class, 'update'])->name('admin.update.email-concern');

        Route::get('/view/software-request/{requestId}', [ViewSoftwareRequestController::class, 'show'])->name('admin.view.software-request');
        Route::post('/view/software-request/{requestId}', [ViewSoftwareRequestController::class, 'update'])->name('admin.update.software-request');

        Route::get('/view/documentation/{requestId}', [ViewDocumentationController::class, 'show'])->name('admin.view.documentation');
        Route::post('/view/documentation/{requestId}', [ViewDocumentationController::class, 'update'])->name('admin.update.documentation');

        Route::get('/view/audio-visual-editing/{requestId}', [ViewAudioVisualEditingController::class, 'show'])->name('admin.view.audio-visual-editing');
        Route::post('/view/audio-visual-editing/{requestId}', [ViewAudioVisualEditingController::class, 'update'])->name('admin.update.audio-visual-editing');

        Route::get('/view/id-card-printing/{requestId}', [ViewIdCardPrintingController::class, 'show'])->name('admin.view.id-card-printing');
        Route::post('/view/id-card-printing/{requestId}', [ViewIdCardPrintingController::class, 'update'])->name('admin.update.id-card-printing');

        // Backward compatibility routes for old email request types
        Route::get('/view/deped-email-request/{requestId}', [ViewEmailManagementController::class, 'show'])->name('admin.view.deped-email-request');
        Route::post('/view/deped-email-request/{requestId}', [ViewEmailManagementController::class, 'update'])->name('admin.update.deped-email-request');

        Route::get('/view/password-reset/{requestId}', [ViewEmailManagementController::class, 'show'])->name('admin.view.password-reset');
        Route::post('/view/password-reset/{requestId}', [ViewEmailManagementController::class, 'update'])->name('admin.update.password-reset');
    });
});
