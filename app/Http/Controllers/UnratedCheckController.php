<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnratedCheckController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $hasUnrated = ServiceRequest::forUser((int) $request->user()->getAuthIdentifier())
            ->where('stat', 'Completed')
            ->where('rated', false)
            ->exists();

        return response()->json(['has_unrated' => $hasUnrated]);
    }
}
