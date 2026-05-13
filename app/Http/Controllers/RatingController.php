<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function updateRated(Request $request, int $requestId): JsonResponse
    {
        $serviceRequest = ServiceRequest::forUser($request->user()->getAuthIdentifier())
            ->where('request_id', $requestId)
            ->firstOrFail();

        $serviceRequest->update(['rated' => true]);

        return response()->json(['success' => true]);
    }
}
