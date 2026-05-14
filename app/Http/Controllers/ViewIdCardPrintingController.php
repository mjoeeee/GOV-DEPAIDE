<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ViewIdCardPrintingController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $query = ServiceRequest::where('request_id', $requestId);

        // Only apply user filter if not an admin
        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();

        $idCard = $serviceRequest->idCardPrinting;

        return Inertia::render('ViewIdCardPrinting', [
            'serviceRequest' => $serviceRequest,
            'idCard' => [
                'request_id' => $idCard->request_id,
                'email' => $idCard->email,
                'dep_id' => $idCard->dep_id,
                'role' => $idCard->role,
                'job_title' => $idCard->job_title,
                'hr_id' => $idCard->hr_id,
                'bday' => is_string($idCard->bday) ? $idCard->bday : $idCard->bday?->format('Y-m-d'),
                'emp_id' => $idCard->emp_id,
                'prc_no' => $idCard->prc_no,
                'emrgncy_no' => $idCard->emrgncy_no,
                'emrgncy_name' => $idCard->emrgncy_name,
                'emrgncy_email' => $idCard->emrgncy_email,
                'prfx_name' => $idCard->prfx_name,
                'fname' => $idCard->fname,
                'lname' => $idCard->lname,
                'mname' => $idCard->mname,
                'ext_name' => $idCard->ext_name,
            ],
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

        $idCard = $serviceRequest->idCardPrinting;

        $validated = $request->validate([
            'email' => 'nullable|email|max:255',
            'depId' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'jobTitle' => 'nullable|string|max:255',
            'hrId' => 'nullable|string|max:255',
            'bday' => 'nullable|date',
            'empId' => 'nullable|string|max:255',
            'prcNo' => 'nullable|string|max:255',
            'emrgncyNo' => 'nullable|string|max:255',
            'emrgncyName' => 'nullable|string|max:255',
            'emrgncyEmail' => 'nullable|email|max:255',
            'fname' => 'nullable|string|max:255',
            'lname' => 'nullable|string|max:255',
            'mname' => 'nullable|string|max:255',
            'extName' => 'nullable|string|max:255',
        ]);

        $idCard->email = $validated['email'] ?? $idCard->email;
        $idCard->dep_id = $validated['depId'] ?? $idCard->dep_id;
        $idCard->role = $validated['role'] ?? $idCard->role;
        $idCard->job_title = $validated['jobTitle'] ?? $idCard->job_title;
        $idCard->hr_id = $validated['hrId'] ?? $idCard->hr_id;
        $idCard->bday = $validated['bday'] ?? $idCard->bday;
        $idCard->emp_id = $validated['empId'] ?? $idCard->emp_id;
        $idCard->prc_no = $validated['prcNo'] ?? $idCard->prc_no;
        $idCard->emrgncy_no = $validated['emrgncyNo'] ?? $idCard->emrgncy_no;
        $idCard->emrgncy_name = $validated['emrgncyName'] ?? $idCard->emrgncy_name;
        $idCard->emrgncy_email = $validated['emrgncyEmail'] ?? $idCard->emrgncy_email;
        $idCard->fname = $validated['fname'] ?? $idCard->fname;
        $idCard->lname = $validated['lname'] ?? $idCard->lname;
        $idCard->mname = $validated['mname'] ?? $idCard->mname;
        $idCard->ext_name = $validated['extName'] ?? $idCard->ext_name;
        $idCard->save();

        return redirect()->route('status.view.id-card-printing', ['requestId' => $requestId, 'update' => 'success']);
    }
}
