<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminRequestController extends Controller
{
    public function index(): Response
    {
        $requests = ServiceRequest::with('user')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn ($req) => [
                'request_id' => $req->request_id,
                'user_name' => $req->user->fullname,
                'request_type_table' => $req->request_type_table,
                'mapped_type' => $req->mapped_type,
                'stat' => $req->stat,
                'remarks' => $req->remarks,
                'created_at' => $req->created_at->format('m/d/Y • g:i A'),
                'updated_at' => $req->updated_at->toISOString(),
            ]);

        return Inertia::render('Admin/Requests', [
            'requests' => $requests,
            'typeMap' => ServiceRequest::TYPE_MAP,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'stat' => 'required|in:Pending,In Progress,Completed,Rejected',
            'remarks' => 'nullable|string',
        ]);

        $serviceRequest = ServiceRequest::where('request_id', $id)->firstOrFail();
        $serviceRequest->update($validated);

        return redirect()->back()->with('success', 'Request updated successfully.');
    }
}
