<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    protected function handleSuccess(string $message): JsonResponse
    {
        return response()->json(['message' => $message]);
    }

    protected function handleException(Exception $e, string $message, int $statusCode = 500): JsonResponse
    {
        Log::error($e->getMessage());

        return response()->json([
            'message' => $message,
        ], $statusCode);
    }
}
