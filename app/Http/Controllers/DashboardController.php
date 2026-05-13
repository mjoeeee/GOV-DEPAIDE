<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = (int) $request->user()->getAuthIdentifier();

        $ictMaintenanceInspectionCount = ServiceRequest::forUser($userId)
            ->whereIn('request_type_table', ['ict_maintenance_inspection', 'ict_maintenance', 'ict_equipment_inspection'])
            ->count();

        $emailManagementCount = ServiceRequest::forUser($userId)
            ->whereIn('request_type_table', ['email_management', 'deped_email_request', 'password_reset'])
            ->count();

        $documentationCount = ServiceRequest::forUser($userId)
            ->where('request_type_table', 'documentation')
            ->count();

        $softwareDevelopmentCount = ServiceRequest::forUser($userId)
            ->where('request_type_table', 'software_development')
            ->count();

        $audioVisualEditingCount = ServiceRequest::forUser($userId)
            ->where('request_type_table', 'audio_visual_editing')
            ->count();

        $idCardPrintingCount = ServiceRequest::forUser($userId)
            ->where('request_type_table', 'id_card_printing')
            ->count();

        return Inertia::render('Dashboard', [
            'ict_maintenance_inspection_count' => $ictMaintenanceInspectionCount,
            'email_management_count' => $emailManagementCount,
            'documentation_count' => $documentationCount,
            'software_development_count' => $softwareDevelopmentCount,
            'audio_visual_editing_count' => $audioVisualEditingCount,
            'id_card_printing_count' => $idCardPrintingCount,
        ]);
    }
}
