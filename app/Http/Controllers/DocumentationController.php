<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DocumentationController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Documentation');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'nullable|string|max:10',
            'end_time' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'photo_link' => 'nullable|url|max:255',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => (int) $request->user()->getAuthIdentifier(),
            'request_type_table' => 'documentation',
            'stat' => 'Pending',
        ]);

        if (Schema::hasTable('tbl_document_depaide')) {
            DB::table('tbl_document_depaide')->insert([
                'title' => $validated['title'],
                'event_location' => $validated['event_location'],
                'event_date' => $validated['event_date'],
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'description' => $validated['description'] ?? null,
                'details' => $validated['details'] ?? null,
                'photo_link' => $validated['photo_link'] ?? null,
            ]);
        } else {
            $documentation = new Documentation([
                'title' => $validated['title'],
                'event_location' => $validated['event_location'],
                'event_date' => $validated['event_date'],
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'description' => $validated['description'] ?? null,
                'details' => $validated['details'] ?? null,
                'photo_link' => $validated['photo_link'] ?? null,
            ]);
            $documentation->request_id = $serviceRequest->request_id;
            $documentation->save();
        }

        return redirect()->route('documentation.create')->with('success', 'Documentation request submitted successfully!');
    }
}
