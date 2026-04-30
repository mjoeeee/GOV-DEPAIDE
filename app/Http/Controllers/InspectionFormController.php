<?php

namespace App\Http\Controllers;

use App\Models\IctEquipmentInspection;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InspectionFormController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('InspectionForm');
    }

    public function store(Request $request): RedirectResponse
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
            'user_id' => $request->user()->id,
            'request_type_table' => 'ict_equipment_inspection',
            'stat' => 'Pending',
        ]);

        IctEquipmentInspection::create([
            'request_id' => $serviceRequest->request_id,
            'item' => $validated['item'],
            'property_no' => $validated['propertyNo'] ?? null,
            'receipt_no' => $validated['receiptNo'] ?? null,
            'acquisition_cost' => $validated['acquisitionCost'] ?? null,
            'acquisition_date' => $validated['acquisitionDate'] ?? null,
            'complaints' => $validated['complaints'],
            'scope_last_repair' => $validated['scopeLastRepair'] ?? null,
        ]);

        return redirect()->route('inspection-form.create')->with('success', 'Inspection request submitted successfully!');
    }
}
