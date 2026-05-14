<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ViewSoftwareRequestController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $query = ServiceRequest::where('request_id', $requestId);

        // Only apply user filter if not an admin
        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();

        $software = $serviceRequest->softwareDevelopment;

        return Inertia::render('ViewSoftwareRequest', [
            'serviceRequest' => $serviceRequest,
            'software' => [
                'id' => $software->id,
                'request_id' => $software->request_id,
                'proj_name' => $software->proj_name,
                'brief_desc' => $software->brief_desc,
                'prime_obj' => $software->prime_obj,
                'features' => $software->features,
                'spec' => $software->spec,
                'attachment' => $software->attachment,
                'attachment_url' => $software->attachment ? Storage::url($software->attachment) : null,
                'proj_deadline' => $software->proj_deadline?->format('Y-m-d\TH:i'),
                'add_info' => $software->add_info,
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

        $software = $serviceRequest->softwareDevelopment;

        $validated = $request->validate([
            'projName' => 'required|string|max:255',
            'briefDesc' => 'nullable|string',
            'primeObj' => 'nullable|string',
            'features' => 'nullable|string',
            'spec' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
            'projDeadline' => 'nullable|date',
            'addInfo' => 'nullable|string',
        ]);

        $software->proj_name = $validated['projName'];
        $software->brief_desc = $validated['briefDesc'] ?? null;
        $software->prime_obj = $validated['primeObj'] ?? null;
        $software->features = $validated['features'] ?? null;
        $software->spec = $validated['spec'] ?? null;
        $software->proj_deadline = $validated['projDeadline'] ?? null;
        $software->add_info = $validated['addInfo'] ?? null;

        if ($request->hasFile('attachment')) {
            if ($software->attachment) {
                Storage::disk('public')->delete($software->attachment);
            }
            $software->attachment = $request->file('attachment')->store('attachments/software_dev', 'public');
        }
        $software->save();

        return redirect()->route('status.view.software-request', ['requestId' => $requestId, 'update' => 'success']);
    }
}
