<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailConcernController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('EmailConcern');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'attachment' => 'nullable|image|max:5120',
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments/email_concern', 'public');
        }

        $serviceRequest = ServiceRequest::create([
            'user_id' => $request->user()->id,
            'request_type_table' => 'password_reset',
            'stat' => 'Pending',
        ]);

        PasswordResetRequest::create([
            'request_id' => $serviceRequest->request_id,
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('email-concern.create')->with('success', 'Email concern submitted successfully!');
    }
}
