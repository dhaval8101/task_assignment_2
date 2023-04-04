<?php


    function successResponse($data, $message = '')
    {
        return response()->json([
            'success' => 200,
            'message' => $message,
            'data' => $data
        ]);
    }
    function errorResponse($message, $code)
    {
        return response()->json([
            'success' => 403,
            'message' => $message,
        ], $code);
    }
    