<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

abstract class BaseApiController extends Controller
{
    protected function success(mixed $data, int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json(['data' => $data, 'status' => 'success'], $status);
    }

    protected function noContent(): \Illuminate\Http\JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function error(string $message, int $status = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => $message, 'status' => 'error'], $status);
    }
}
