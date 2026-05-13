<?php

namespace App\Http\Controllers;

use App\Models\IdCardPrinting;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IdCardPrintingController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('IdCardPrinting');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'depId' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'jobTitle' => 'required|string|max:255',
            'prfxName' => 'nullable|string|max:50',
            'hrId' => 'nullable|string|max:255',
            'bday' => 'nullable|date',
            'empId' => 'nullable|string|max:255',
            'prcNo' => 'nullable|string|max:255',
            'tinNo' => 'nullable|string|max:255',
            'gsisNo' => 'nullable|string|max:255',
            'pagibigNo' => 'nullable|string|max:255',
            'philhealthNo' => 'nullable|string|max:255',
            'bloodType' => 'nullable|string|max:10',
            'emrgncyName' => 'nullable|string|max:255',
            'emrgncyNo' => 'nullable|string|max:255',
            'emrgncyEmail' => 'nullable|email|max:255',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'extName' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg|max:2048',
            'sign' => 'nullable|image|mimes:png|max:2048',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => (int) $request->user()->getAuthIdentifier(),
            'request_type_table' => 'id_card_printing',
            'stat' => 'Pending',
        ]);

        $idCard = new IdCardPrinting([
            'email' => $validated['email'],
            'dep_id' => $validated['depId'],
            'role' => $validated['role'],
            'job_title' => $validated['jobTitle'],
            'prfx_name' => $validated['prfxName'] ?? null,
            'hr_id' => $validated['hrId'] ?? null,
            'bday' => $validated['bday'] ?? null,
            'emp_id' => $validated['empId'] ?? null,
            'prc_no' => $validated['prcNo'] ?? null,
            'tin_no' => $validated['tinNo'] ?? null,
            'gsis_no' => $validated['gsisNo'] ?? null,
            'pagibig_no' => $validated['pagibigNo'] ?? null,
            'philhealth_no' => $validated['philhealthNo'] ?? null,
            'blood_type' => $validated['bloodType'] ?? null,
            'emrgncy_name' => $validated['emrgncyName'] ?? null,
            'emrgncy_no' => $validated['emrgncyNo'] ?? null,
            'emrgncy_email' => $validated['emrgncyEmail'] ?? null,
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'mname' => $validated['mname'] ?? null,
            'ext_name' => $validated['extName'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            $idCard->image = $request->file('image')->store('id-card-printing/photos', 'public');
        }

        if ($request->hasFile('sign')) {
            $idCard->sign = $request->file('sign')->store('id-card-printing/signatures', 'public');
        }

        $idCard->request_id = $serviceRequest->request_id;
        $idCard->save();

        return redirect()->route('id-card-printing.create')->with('success', 'ID Card Printing request submitted successfully!');
    }
}
