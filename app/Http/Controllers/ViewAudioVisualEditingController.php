<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ViewAudioVisualEditingController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $query = ServiceRequest::where('request_id', $requestId);

        // Only apply user filter if not an admin
        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();

        $audioVisual = $serviceRequest->audioVisualEditing;

        return Inertia::render('ViewAudioVisualEditing', [
            'serviceRequest' => $serviceRequest,
            'audioVisual' => [
                'request_id' => $audioVisual->request_id,
                'title' => $audioVisual->title,
                'project_type' => $audioVisual->project_type,
                'delivery_method' => $audioVisual->delivery_method,
                'project_deadline' => is_string($audioVisual->project_deadline) ? $audioVisual->project_deadline : $audioVisual->project_deadline?->format('Y-m-d'),
                'proj_desc' => $audioVisual->proj_desc,
                'music_preference' => $audioVisual->music_preference,
                'deliverables' => $audioVisual->deliverables,
                'style_tone' => $audioVisual->style_tone,
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

        $audioVisual = $serviceRequest->audioVisualEditing;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'projectType' => 'nullable|string|max:255',
            'deliveryMethod' => 'nullable|string|max:255',
            'projectDeadline' => 'nullable|date',
            'projDesc' => 'nullable|string',
            'musicPreference' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'styleTone' => 'nullable|string',
        ]);

        $audioVisual->title = $validated['title'];
        $audioVisual->project_type = $validated['projectType'] ?? null;
        $audioVisual->delivery_method = $validated['deliveryMethod'] ?? null;
        $audioVisual->project_deadline = $validated['projectDeadline'] ?? null;
        $audioVisual->proj_desc = $validated['projDesc'] ?? null;
        $audioVisual->music_preference = $validated['musicPreference'] ?? null;
        $audioVisual->deliverables = $validated['deliverables'] ?? null;
        $audioVisual->style_tone = $validated['styleTone'] ?? null;
        $audioVisual->save();

        return redirect()->route('status.view.audio-visual-editing', ['requestId' => $requestId, 'update' => 'success']);
    }
}
