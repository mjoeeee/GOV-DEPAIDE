<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
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
        ];
    }
}
