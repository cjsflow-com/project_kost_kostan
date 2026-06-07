<?php

class BaseResponse
{
    public static function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message = 'Error', $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => "$message => {$errors[0]}", // Ambil pesan error pertama jika ada
            'errors' => $errors
        ], $code);
    }
}
