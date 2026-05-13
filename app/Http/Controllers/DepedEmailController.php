<?php

namespace App\Http\Controllers;

use App\Models\DepedEmailRequest;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DepedEmailController extends Controller
{
    public function create(Request $request): Response
    {
        $userId = (int) $request->user()->getAuthIdentifier();

        $alreadyRequested = ServiceRequest::forUser($userId)
            ->where('request_type_table', 'deped_email_request')
            ->exists();

        return Inertia::render('DepedEmail', [
            'already_requested' => $alreadyRequested,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $userId = (int) $user->getAuthIdentifier();

        $alreadyRequested = ServiceRequest::forUser($userId)
            ->where('request_type_table', 'deped_email_request')
            ->exists();

        if ($alreadyRequested) {
            return redirect()->route('deped-email.create')->with('error', 'You have already submitted a DepEd Email request.');
        }

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

        return redirect()->route('deped-email.create')->with('success', 'DepEd Email request submitted successfully!');
    }
}
