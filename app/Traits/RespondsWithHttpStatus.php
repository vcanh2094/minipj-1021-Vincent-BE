<?php
namespace App\Traits;

trait RespondsWithHttpStatus
{
    protected function success($message, $status){
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $status);
    }
    protected function successWithData($message, $data = [], $status = 200){
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
    protected function fails($message, $status = 401){
        return response([
            'success' => false,
            'error' => $message,
        ], $status);
    }

}
