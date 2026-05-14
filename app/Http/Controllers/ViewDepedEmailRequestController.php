<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ViewDepedEmailRequestController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $query = ServiceRequest::where('request_id', $requestId);

        // Only apply user filter if not an admin
        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();

        $depedEmail = $serviceRequest->depedEmailRequest;

        return Inertia::render('ViewDepedEmailRequest', [
            'serviceRequest' => $serviceRequest,
            'depedEmail' => [
                'request_id' => $depedEmail->request_id,
                'school_id' => $depedEmail->school_id,
                'office_id' => $depedEmail->office_id,
                'firstname' => $depedEmail->firstname,
                'lastname' => $depedEmail->lastname,
                'suffix' => $depedEmail->suffix,
                'position' => $depedEmail->position,
                'email_format' => $depedEmail->email_format,
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

        $depedEmail = $serviceRequest->depedEmailRequest;

        $validated = $request->validate([
            'schoolId' => 'nullable|string|max:255',
            'officeId' => 'nullable|string|max:255',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'emailFormat' => 'nullable|email|max:255',
        ]);

        $depedEmail->school_id = $validated['schoolId'] ?? $depedEmail->school_id;
        $depedEmail->office_id = $validated['officeId'] ?? $depedEmail->office_id;
        $depedEmail->firstname = $validated['firstname'] ?? $depedEmail->firstname;
        $depedEmail->lastname = $validated['lastname'] ?? $depedEmail->lastname;
        $depedEmail->suffix = $validated['suffix'] ?? $depedEmail->suffix;
        $depedEmail->position = $validated['position'] ?? $depedEmail->position;
        $depedEmail->email_format = $validated['emailFormat'] ?? $depedEmail->email_format;
        $depedEmail->save();

        return redirect()->route('status.view.deped-email-request', ['requestId' => $requestId, 'update' => 'success']);
    }
}
