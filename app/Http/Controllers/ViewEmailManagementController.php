<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ViewEmailManagementController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $query = ServiceRequest::where('request_id', $requestId);

        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();
        $emailData = $this->getEmailData($serviceRequest);

        return Inertia::render('ViewEmailManagement', [
            'serviceRequest' => $serviceRequest,
            'emailManagement' => $emailData,
        ]);
    }

    public function update(Request $request, int $requestId): RedirectResponse
    {
        $query = ServiceRequest::where('request_id', $requestId);

        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();
        $emailData = $this->getEmailData($serviceRequest);

        if ($emailData['type'] === 'deped_email') {
            return $this->updateDepedEmail($request, $serviceRequest, $emailData);
        } else {
            return $this->updateEmailConcern($request, $serviceRequest, $emailData);
        }
    }

    private function getEmailData(ServiceRequest $serviceRequest): array
    {
        if ($serviceRequest->depedEmailRequest) {
            $deped = $serviceRequest->depedEmailRequest;

            return [
                'type' => 'deped_email',
                'request_type_label' => 'DepEd Email Request',
                'request_id' => $deped->request_id,
                'school_id' => $deped->school_id,
                'office_id' => $deped->office_id,
                'email_format' => $deped->email_format,
                'firstname' => $deped->firstname,
                'lastname' => $deped->lastname,
                'suffix' => $deped->suffix,
                'position' => $deped->position,
            ];
        }

        if ($serviceRequest->passwordResetRequest) {
            $reset = $serviceRequest->passwordResetRequest;

            return [
                'type' => 'email_concern',
                'request_type_label' => 'Email Concern',
                'request_id' => $reset->request_id,
                'reason' => $reset->reason,
                'attachment' => $reset->attachment,
                'attachment_url' => $reset->attachment ? Storage::url($reset->attachment) : null,
                'email' => $reset->email,
            ];
        }

        return [];
    }

    private function updateDepedEmail(Request $request, ServiceRequest $serviceRequest, array $emailData): RedirectResponse
    {
        $validated = $request->validate([
            'officeId' => 'required|string|max:255',
            'emailFormat' => 'required|email|max:255',
        ]);

        $deped = $serviceRequest->depedEmailRequest;
        $deped->update([
            'office_id' => $validated['officeId'],
            'email_format' => $validated['emailFormat'],
        ]);

        return redirect("/status/view/email-management/{$serviceRequest->request_id}")->with('update', 'success');
    }

    private function updateEmailConcern(Request $request, ServiceRequest $serviceRequest, array $emailData): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'attachment' => 'nullable|image|max:5120',
        ]);

        $reset = $serviceRequest->passwordResetRequest;

        if ($request->hasFile('attachment')) {
            if ($reset->attachment && Storage::exists($reset->attachment)) {
                Storage::delete($reset->attachment);
            }
            $attachmentPath = $request->file('attachment')->store('attachments/email_management', 'public');
            $reset->attachment = $attachmentPath;
        }

        $reset->reason = $validated['reason'];
        $reset->save();

        return redirect("/status/view/email-management/{$serviceRequest->request_id}")->with('update', 'success');
    }
}
