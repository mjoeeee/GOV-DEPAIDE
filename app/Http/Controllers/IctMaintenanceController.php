<?php

namespace App\Http\Controllers;

use App\Models\IctMaintenance;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IctMaintenanceController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('IctMaintenance', [
            'divisionOffices' => config('division_offices'),
        ]);
    }

    public function store(Request $request): RedirectResponse
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
            'user_id' => $request->user()->id,
            'request_type_table' => 'ict_maintenance',
            'stat' => 'Pending',
        ]);

        IctMaintenance::create([
            'request_id' => $serviceRequest->request_id,
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

        return redirect()->route('ict-maintenance.create')->with('success', 'ICT Maintenance request submitted successfully!');
    }
}
