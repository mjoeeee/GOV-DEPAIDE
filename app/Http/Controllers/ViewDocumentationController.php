<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ViewDocumentationController extends Controller
{
    public function show(Request $request, int $requestId): Response
    {
        $query = ServiceRequest::where('request_id', $requestId);

        // Only apply user filter if not an admin
        if (! $request->user()->isAdmin()) {
            $query = $query->forUser($request->user()->getAuthIdentifier());
        }

        $serviceRequest = $query->firstOrFail();

        $documentation = $serviceRequest->documentation;

        return Inertia::render('ViewDocumentation', [
            'serviceRequest' => $serviceRequest,
            'documentation' => [
                'request_id' => $documentation->request_id,
                'title' => $documentation->title,
                'event_location' => $documentation->event_location,
                'event_date' => is_string($documentation->event_date) ? $documentation->event_date : $documentation->event_date?->format('Y-m-d'),
                'start_time' => $documentation->start_time,
                'end_time' => $documentation->end_time,
                'description' => $documentation->description,
                'details' => $documentation->details,
                'photo_link' => $documentation->photo_link,
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

        $documentation = $serviceRequest->documentation;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'eventLocation' => 'nullable|string|max:255',
            'eventDate' => 'nullable|date',
            'startTime' => 'nullable',
            'endTime' => 'nullable',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'photoLink' => 'nullable|url|max:255',
        ]);

        $documentation->title = $validated['title'];
        $documentation->event_location = $validated['eventLocation'] ?? null;
        $documentation->event_date = $validated['eventDate'] ?? null;
        $documentation->start_time = $validated['startTime'] ?? null;
        $documentation->end_time = $validated['endTime'] ?? null;
        $documentation->description = $validated['description'] ?? null;
        $documentation->details = $validated['details'] ?? null;
        $documentation->photo_link = $validated['photoLink'] ?? null;
        $documentation->save();

        return redirect("/status/view/documentation/{$requestId}?update=success");
    }
}
