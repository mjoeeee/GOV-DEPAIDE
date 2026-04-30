<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(): Response
    {
        $total = ServiceRequest::count();
        $pending = ServiceRequest::where('stat', 'Pending')->count();
        $inProgress = ServiceRequest::where('stat', 'In Progress')->count();
        $completed = ServiceRequest::where('stat', 'Completed')->count();
        $rejected = ServiceRequest::where('stat', 'Rejected')->count();

        $recentRequests = ServiceRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($req) => [
                'request_id' => $req->request_id,
                'user_name' => $req->user->fullname,
                'mapped_type' => $req->mapped_type,
                'stat' => $req->stat,
                'created_at' => $req->created_at->format('m/d/Y • g:i A'),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => compact('total', 'pending', 'inProgress', 'completed', 'rejected'),
            'recentRequests' => $recentRequests,
        ]);
    }
}
