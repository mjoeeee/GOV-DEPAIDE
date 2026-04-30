<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ViewEmailConcernController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $serviceRequest = ServiceRequest::where('request_id', $requestId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $concern = PasswordResetRequest::where('request_id', $requestId)->firstOrFail();

        return Inertia::render('ViewEmailConcern', [
            'serviceRequest' => $serviceRequest,
            'concern' => [
                'id' => $concern->id,
                'request_id' => $concern->request_id,
                'email' => $concern->email,
                'reason' => $concern->reason,
                'attachment' => $concern->attachment,
                'attachment_url' => $concern->attachment ? Storage::url($concern->attachment) : null,
            ],
        ]);
    }

    public function update(Request $request, int $requestId): RedirectResponse
    {
        ServiceRequest::where('request_id', $requestId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $concern = PasswordResetRequest::where('request_id', $requestId)->firstOrFail();

        $validated = $request->validate([
            'reason' => 'required|string',
            'attachment' => 'nullable|image|max:5120',
        ]);

        $concern->reason = $validated['reason'];

        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($concern->attachment) {
                Storage::disk('public')->delete($concern->attachment);
            }

            $concern->attachment = $request->file('attachment')->store('attachments/email_concern', 'public');
        }

        $concern->save();

        return redirect("/status/view/email-concern/{$requestId}?update=success");
    }
}
