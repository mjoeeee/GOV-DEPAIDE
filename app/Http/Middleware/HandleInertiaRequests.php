<?php

namespace App\Http\Middleware;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $adminRequestTypeCounts = [];

        if ($user && in_array(mb_strtolower(trim((string) $user->role)), ['admin', 'system admin'], true)) {
            $adminRequestTypeCounts = ServiceRequest::select('request_type_table', DB::raw('count(*) as pending'))
                ->where('stat', 'Pending')
                ->groupBy('request_type_table')
                ->pluck('pending', 'request_type_table')
                ->toArray();
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'fullname' => $user->fullname,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'extname' => $user->extname,
                    'email' => $user->email,
                    'job_title' => $user->job_title,
                    'role' => $user->role,
                ] : null,
            ],
            'adminRequestTypeCounts' => $adminRequestTypeCounts,
        ];
    }
}
