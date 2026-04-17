<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $service,
        ]);
    }
}
