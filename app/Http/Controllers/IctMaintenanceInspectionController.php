<?php

namespace App\Http\Controllers;

use App\Models\IctMaintenanceInspection;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IctMaintenanceInspectionController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('IctMaintenanceInspection', [
            'divisionOffices' => config('division_offices'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'maintenance');

        if ($type === 'maintenance') {
            return $this->storeMaintenance($request);
        } else {
            return $this->storeInspection($request);
        }
    }

    private function storeMaintenance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'divisionOffice' => 'required|string',
            'propertyDescription' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'propertyNumber' => 'nullable|string|max:255',
            'serialNumber' => 'nullable|string|max:255',
            'lastRepairDate' => 'nullable|date',
            'defects' => 'nullable|string',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => (int) $request->user()->getAuthIdentifier(),
            'request_type_table' => 'ict_maintenance_inspection',
            'stat' => 'Pending',
        ]);

        IctMaintenanceInspection::create([
            'request_id' => $serviceRequest->request_id,
            'type' => 'maintenance',
            'date_current' => $validated['date'],
            'time_current' => $validated['time'],
            'req_name' => $validated['name'],
            'req_designation' => $validated['designation'],
            'req_DO' => $validated['divisionOffice'],
            'DOPE' => $validated['propertyDescription'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'prop_no' => $validated['propertyNumber'] ?? null,
            'serial_no' => $validated['serialNumber'] ?? null,
            'date_last_repair' => $validated['lastRepairDate'] ?? null,
            'defects' => $validated['defects'] ?? null,
        ]);

        return redirect()->route('ict-maintenance-inspection.create')->with('success', 'ICT Maintenance request submitted successfully!');
    }

    private function storeInspection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'propertyNo' => 'nullable|string|max:255',
            'receiptNo' => 'nullable|string|max:255',
            'acquisitionCost' => 'nullable|string|max:255',
            'acquisitionDate' => 'nullable|date',
            'complaints' => 'required|string',
            'scopeLastRepair' => 'nullable|string',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => (int) $request->user()->getAuthIdentifier(),
            'request_type_table' => 'ict_maintenance_inspection',
            'stat' => 'Pending',
        ]);

        IctMaintenanceInspection::create([
            'request_id' => $serviceRequest->request_id,
            'type' => 'inspection',
            'item' => $validated['item'],
            'property_no' => $validated['propertyNo'] ?? null,
            'receipt_no' => $validated['receiptNo'] ?? null,
            'acquisition_cost' => $validated['acquisitionCost'] ?? null,
            'acquisition_date' => $validated['acquisitionDate'] ?? null,
            'complaints' => $validated['complaints'],
            'scope_last_repair' => $validated['scopeLastRepair'] ?? null,
        ]);

        return redirect()->route('ict-maintenance-inspection.create')->with('success', 'ICT Equipment Inspection request submitted successfully!');
    }
}
