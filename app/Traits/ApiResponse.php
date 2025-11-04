<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($data, $message = 'succes !', $count, $user_id,   $code = 200)
    {
        return response()->json([
            'succes' => true,
            'message' => $message,
            'data' => $data,
            'count' => $count,
            'user_id' => $user_id,
        ], $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'succes' => false,
            'message' => $message
        ], $code);
    }
}