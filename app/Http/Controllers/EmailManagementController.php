<?php

namespace App\Http\Controllers;

use App\Models\DepedEmailRequest;
use App\Models\PasswordResetRequest;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class EmailManagementController extends Controller
{
    public function create(Request $request): Response
    {
        $userId = (int) $request->user()->getAuthIdentifier();

        $alreadyRequested = ServiceRequest::forUser($userId)
            ->whereIn('request_type_table', ['deped_email_request', 'password_reset'])
            ->exists();

        return Inertia::render('EmailManagement', [
            'already_requested' => $alreadyRequested,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $userId = (int) $user->getAuthIdentifier();

        $alreadyRequested = ServiceRequest::forUser($userId)
            ->whereIn('request_type_table', ['deped_email_request', 'password_reset'])
            ->exists();

        if ($alreadyRequested) {
            return redirect()->route('email-management.create')->with('error', 'You have already submitted an Email Management request.');
        }

        $type = $request->input('emailType', 'deped_email');

        if ($type === 'deped_email') {
            return $this->storeDepedEmail($request, $user, $userId);
        }

        return $this->storeEmailConcern($request, $user, $userId);
    }

    private function storeDepedEmail(Request $request, $user, $userId): RedirectResponse
    {
        $validated = $request->validate([
            'officeId' => 'required|string|max:255',
            'emailFormat' => 'required|email|max:255',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $userId,
            'request_type_table' => 'deped_email_request',
            'stat' => 'Pending',
        ]);

        if (Schema::hasTable('tbl_depedemail_depaide')) {
            DB::table('tbl_depedemail_depaide')->insert([
                'school_id' => $validated['officeId'],
                'email_format' => $validated['emailFormat'],
            ]);
        } else {
            DepedEmailRequest::create([
                'request_id' => $serviceRequest->request_id,
                'user_id' => $userId,
                'office_id' => $validated['officeId'],
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'suffix' => $user->extname,
                'position' => $user->job_title,
                'email_format' => $validated['emailFormat'],
            ]);
        }

        return redirect()->route('email-management.create')->with('success', 'Email Management request submitted successfully!');
    }

    private function storeEmailConcern(Request $request, $user, $userId): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'attachment' => 'nullable|image|max:5120',
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments/email_management', 'public');
        }

        $serviceRequest = ServiceRequest::create([
            'user_id' => $userId,
            'request_type_table' => 'password_reset',
            'stat' => 'Pending',
        ]);

        if (Schema::hasTable('tbl_passreset_depaide')) {
            $legacyId = DB::table('tbl_passreset_depaide')->insertGetId([
                'reason' => $validated['reason'],
                'attachment' => $attachmentPath,
            ]);

            $serviceRequest->request_type_id = $legacyId;
            $serviceRequest->save();
        } else {
            PasswordResetRequest::create([
                'request_id' => $serviceRequest->request_id,
                'user_id' => $userId,
                'email' => $user->email,
                'reason' => $validated['reason'],
                'attachment' => $attachmentPath,
            ]);
        }

        return redirect()->route('email-management.create')->with('success', 'Email Management request submitted successfully!');
    }
}
