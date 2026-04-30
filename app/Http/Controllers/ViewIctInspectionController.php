<?php

namespace App\Http\Controllers;

use App\Models\IctEquipmentInspection;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ViewIctInspectionController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $serviceRequest = ServiceRequest::where('request_id', $requestId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $inspection = IctEquipmentInspection::where('request_id', $requestId)->firstOrFail();

        return Inertia::render('ViewIctInspection', [
            'serviceRequest' => $serviceRequest,
            'inspection' => [
                'id' => $inspection->id,
                'request_id' => $inspection->request_id,
                'item' => $inspection->item,
                'property_no' => $inspection->property_no,
                'receipt_no' => $inspection->receipt_no,
                'acquisition_cost' => $inspection->acquisition_cost,
                'acquisition_date' => $inspection->acquisition_date?->format('Y-m-d'),
                'complaints' => $inspection->complaints,
                'scope_last_repair' => $inspection->scope_last_repair,
            ],
        ]);
    }

    public function update(Request $request, int $requestId): RedirectResponse
    {
        ServiceRequest::where('request_id', $requestId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $inspection = IctEquipmentInspection::where('request_id', $requestId)->firstOrFail();

        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'propertyNo' => 'nullable|string|max:255',
            'receiptNo' => 'nullable|string|max:255',
            'acquisitionCost' => 'nullable|string|max:255',
            'acquisitionDate' => 'nullable|date',
            'complaints' => 'required|string',
            'scopeLastRepair' => 'nullable|string',
        ]);

        $inspection->update([
            'item' => $validated['item'],
            'property_no' => $validated['propertyNo'] ?? null,
            'receipt_no' => $validated['receiptNo'] ?? null,
            'acquisition_cost' => $validated['acquisitionCost'] ?? null,
            'acquisition_date' => $validated['acquisitionDate'] ?? null,
            'complaints' => $validated['complaints'],
            'scope_last_repair' => $validated['scopeLastRepair'] ?? null,
        ]);

        return redirect("/status/view/ict-inspection/{$requestId}?update=success");
    }
}
