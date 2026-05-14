<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ViewIctMaintenanceController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $query = ServiceRequest::where('request_id', $requestId);

        // Only apply user filter if not an admin
        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();

        $maintenance = $serviceRequest->ictMaintenance;

        return Inertia::render('ViewIctMaintenance', [
            'serviceRequest' => $serviceRequest,
            'maintenance' => [
                'id' => $maintenance->id,
                'request_id' => $maintenance->request_id,
                'date_current' => $maintenance->date_current?->format('Y-m-d'),
                'time_current' => $maintenance->time_current,
                'req_name' => $maintenance->req_name,
                'req_designation' => $maintenance->req_designation,
                'req_DO' => $maintenance->req_DO,
                'DOPE' => $maintenance->DOPE,
                'brand' => $maintenance->brand,
                'prop_no' => $maintenance->prop_no,
                'serial_no' => $maintenance->serial_no,
                'last_repair_date' => $maintenance->last_repair_date?->format('Y-m-d'),
                'defects' => $maintenance->defects,
            ],
            'divisionOffices' => config('division_offices'),
        ]);
    }

    public function update(Request $request, int $requestId): RedirectResponse
    {
        $query = ServiceRequest::where('request_id', $requestId);

        // Only apply user filter if not an admin
        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();

        $maintenance = $serviceRequest->ictMaintenance;

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

        $maintenance->update([
            'date_current' => $validated['date'],
            'time_current' => $validated['time'],
            'req_name' => $validated['name'],
            'req_designation' => $validated['designation'],
            'req_DO' => $validated['divisionOffice'],
            'DOPE' => $validated['propertyDescription'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'prop_no' => $validated['propertyNumber'] ?? null,
            'serial_no' => $validated['serialNumber'] ?? null,
            'last_repair_date' => $validated['lastRepairDate'] ?? null,
            'defects' => $validated['defects'] ?? null,
        ]);

        return redirect()->route('status.view.ict-maintenance', ['requestId' => $requestId, 'update' => 'success']);
    }
}
