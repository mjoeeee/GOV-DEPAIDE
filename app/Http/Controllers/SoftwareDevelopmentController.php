<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\SoftwareDevelopment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SoftwareDevelopmentController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('SoftwareDevelopment');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'projName' => 'required|string|max:255',
            'briefDesc' => 'required|string',
            'primeObj' => 'nullable|string',
            'features' => 'nullable|string',
            'spec' => 'nullable|string',
            'projDeadline' => 'nullable|date',
            'addInfo' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments/software_dev', 'public');
        }

        $serviceRequest = ServiceRequest::create([
            'user_id' => (int) $request->user()->getAuthIdentifier(),
            'request_type_table' => 'software_development',
            'stat' => 'Pending',
        ]);

        $software = new SoftwareDevelopment([
            'proj_name' => $validated['projName'],
            'brief_desc' => $validated['briefDesc'],
            'prime_obj' => $validated['primeObj'] ?? null,
            'features' => $validated['features'] ?? null,
            'spec' => $validated['spec'] ?? null,
            'proj_deadline' => $validated['projDeadline'] ?? null,
            'add_info' => $validated['addInfo'] ?? null,
            'attachment' => $attachmentPath,
        ]);
        $software->request_id = $serviceRequest->request_id;
        $software->save();

        return redirect()->route('software-request.create')->with('success', 'Software development request submitted successfully!');
    }
}
