<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;
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
                'user_name' => $req->user?->fullname ?? 'Unknown User',
                'mapped_type' => $req->mapped_type,
                'stat' => $req->stat,
                'created_at' => $req->created_at->format('m/d/Y • g:i A'),
            ]);

        $typeCounts = ServiceRequest::select('request_type_table', DB::raw('count(*) as total'))
            ->groupBy('request_type_table')
            ->pluck('total', 'request_type_table')
            ->toArray();

        // Merge old ICT types with new merged type for backward compatibility
        if (isset($typeCounts['ict_maintenance'], $typeCounts['ict_equipment_inspection'])) {
            $typeCounts['ict_maintenance_inspection'] = ($typeCounts['ict_maintenance'] ?? 0) + ($typeCounts['ict_equipment_inspection'] ?? 0) + ($typeCounts['ict_maintenance_inspection'] ?? 0);
        }

        $typeCards = collect(ServiceRequest::TYPE_MAP)->map(fn ($label, $key) => [
            'key' => $key,
            'label' => $label,
            'count' => $typeCounts[$key] ?? 0,
            'pending' => $key === 'ict_maintenance_inspection'
                ? ServiceRequest::whereIn('request_type_table', ['ict_maintenance_inspection', 'ict_maintenance', 'ict_equipment_inspection'])->where('stat', 'Pending')->count()
                : ServiceRequest::where('request_type_table', $key)->where('stat', 'Pending')->count(),
            'inProgress' => $key === 'ict_maintenance_inspection'
                ? ServiceRequest::whereIn('request_type_table', ['ict_maintenance_inspection', 'ict_maintenance', 'ict_equipment_inspection'])->where('stat', 'In Progress')->count()
                : ServiceRequest::where('request_type_table', $key)->where('stat', 'In Progress')->count(),
            'url' => route('admin.requests', ['request_type_table' => $key]),
        ])->values()->all();

        $monthlyTotals = ServiceRequest::selectRaw(
            config('database.default') === 'sqlite'
                ? "strftime('%m', created_at) as month, COUNT(*) as total"
                : 'MONTH(created_at) as month, COUNT(*) as total'
        )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => (int) $row->month,
                'total' => (int) $row->total,
            ])
            ->all();

        return Inertia::render('Admin/Dashboard', [
            'stats' => compact('total', 'pending', 'inProgress', 'completed', 'rejected'),
            'recentRequests' => $recentRequests,
            'typeCards' => $typeCards,
            'monthlyTotals' => $monthlyTotals,
        ]);
    }
}
