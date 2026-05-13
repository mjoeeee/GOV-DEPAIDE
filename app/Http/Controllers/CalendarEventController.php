<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ServiceRequest::query();

        if (! $request->user()->isAdmin()) {
            $query->forUser((int) $request->user()->getAuthIdentifier());
        }

        $events = $query
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($req) => [
                'request_id' => $req->request_id,
                'request_type_table' => $req->request_type_table,
                'title' => $req->mapped_type,
                'start' => $req->created_at->toISOString(),
                'stat' => $req->stat,
                'remarks' => $req->remarks,
            ]);

        return response()->json($events);
    }
}
