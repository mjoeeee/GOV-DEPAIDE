<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            return redirect()->route('dashboard');
        }

        $normalizedRole = mb_strtolower(trim($role));
        $normalizedUserRole = mb_strtolower(trim((string) $user->role));

        if ($normalizedRole === 'admin') {
            if (! $user->isAdmin()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Forbidden.'], 403);
                }

                return redirect()->route('dashboard');
            }
        } elseif ($normalizedUserRole !== $normalizedRole) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
