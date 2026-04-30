<?php

namespace App\Http\Controllers;

use App\Models\DepedEmailRequest;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        $ictMaintenanceCount = ServiceRequest::where('user_id', $userId)
            ->where('request_type_table', 'ict_maintenance')
            ->count();

        $inspectionCount = ServiceRequest::where('user_id', $userId)
            ->where('request_type_table', 'ict_equipment_inspection')
            ->count();

        $emailConcernCount = ServiceRequest::where('user_id', $userId)
            ->where('request_type_table', 'password_reset')
            ->count();

        $depedEmailCount = ServiceRequest::where('user_id', $userId)
            ->where('request_type_table', 'deped_email_request')
            ->count();

        $depedEmailAlreadyRequested = DepedEmailRequest::where('user_id', $userId)->exists();

        return Inertia::render('Dashboard', [
            'ict_maintenance_count' => $ictMaintenanceCount,
            'inspection_count' => $inspectionCount,
            'email_concern_count' => $emailConcernCount,
            'deped_email_count' => $depedEmailCount,
            'deped_email_already_requested' => $depedEmailAlreadyRequested,
        ]);
    }
}
