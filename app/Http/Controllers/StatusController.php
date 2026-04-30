<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StatusController extends Controller
{
    public function index(Request $request): Response
    {
        $requests = ServiceRequest::where('user_id', $request->user()->id)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($req) {
                return [
                    'request_id' => $req->request_id,
                    'request_type_table' => $req->request_type_table,
                    'mapped_type' => $req->mapped_type,
                    'stat' => $req->stat,
                    'remarks' => $req->remarks,
                    'rated' => $req->rated,
                    'created_at' => $req->created_at->format('m/d/Y • g:i A'),
                    'updated_at' => $req->updated_at->toISOString(),
                ];
            });

        return Inertia::render('Status', [
            'requests' => $requests,
            'typeMap' => ServiceRequest::TYPE_MAP,
        ]);
    }
}
