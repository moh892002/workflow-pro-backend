<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function success(mixed $data = null, ?string $message = null, int $status = 200, int $options = 0): JsonResponse
    {
        $response = ['success' => true];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($message !== null) {
            $response['message'] = $message;
        }

        return response()->json($response, $status, [], $options);
    }

    protected function created(mixed $data = null, ?string $message = null, int $options = 0): JsonResponse
    {
        return $this->success($data, $message, 201, $options);
    }

    protected function message(string $message, int $status = 200): JsonResponse
    {
        return $this->success(null, $message, $status);
    }

    protected function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
