<?php

namespace App\Http\Controllers;

use App\Models\AudioVisualEditing;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class AudioVisualEditingController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('AudioVisualEditing');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'projectType' => 'required|string|max:255',
            'deliveryMethod' => 'required|string|max:255',
            'projectDeadline' => 'nullable|date',
            'projDesc' => 'nullable|string',
            'musicPreference' => 'nullable|string|max:255',
            'deliverables' => 'nullable|string',
            'styleTone' => 'nullable|string|max:255',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => (int) $request->user()->getAuthIdentifier(),
            'request_type_table' => 'audio_visual_editing',
            'stat' => 'Pending',
        ]);

        if (Schema::hasTable('tbl_audiovisual_depaide')) {
            DB::table('tbl_audiovisual_depaide')->insert([
                'title' => $validated['title'],
                'project_type' => $validated['projectType'],
                'delivery_method' => $validated['deliveryMethod'],
                'project_deadline' => $validated['projectDeadline'] ?? null,
                'proj_desc' => $validated['projDesc'] ?? null,
                'music_preference' => $validated['musicPreference'] ?? null,
                'deliverables' => $validated['deliverables'] ?? null,
                'style_tone' => $validated['styleTone'] ?? null,
            ]);
        } else {
            $audioVisual = new AudioVisualEditing([
                'title' => $validated['title'],
                'project_type' => $validated['projectType'],
                'delivery_method' => $validated['deliveryMethod'],
                'project_deadline' => $validated['projectDeadline'] ?? null,
                'proj_desc' => $validated['projDesc'] ?? null,
                'music_preference' => $validated['musicPreference'] ?? null,
                'deliverables' => $validated['deliverables'] ?? null,
                'style_tone' => $validated['styleTone'] ?? null,
            ]);
            $audioVisual->request_id = $serviceRequest->request_id;
            $audioVisual->save();
        }

        return redirect()->route('audio-visual.create')->with('success', 'Audio visual editing request submitted successfully!');
    }
}
