<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseService
{
    public static function success($message = 'Success', $data = [], $variables = [], $others = [], $code = Response::HTTP_OK, $enableMultipleSteps = false): JsonResponse
    {
        return response()->json(
            [
                'process' => true,
                'message' => $message,
                'data' => $data,
                'errors' => [],
            ],
            $code
        );
    }

    public static function successPersonalized($message = 'Success', $data = [], $variables = [], $others = [], $code = Response::HTTP_OK, $enableMultipleSteps = false): JsonResponse
    {
        return response()->json(
            $data,
            $code
        );
    }

    public static function create($message = 'Success', $data = [], $others = [], $code = Response::HTTP_CREATED): JsonResponse
    {
        return response()->json(
            [
                'process' => true,
                'message' => $message,
                'data' => $data,
                'errors' => [],
            ],
            $code
        );
    }

    public static function error($message = 'Error', $errors = [], $data = [], $others = [], $code = 500): JsonResponse
    {
        if (! is_numeric($code) || $code < 100 || $code >= 600) {
            $code = 500;
        } else {
            $code = (int) $code;
        }

        return response()->json(
            [
                'process' => false,
                'message' => $message,
                'errors' => $errors,
                'data' => $data,
            ],
            $code
        );
    }

    public static function unauthorized($message = 'Unauthorized'): JsonResponse
    {
        return response()->json(
            [
                'process' => false,
                'message' => $message,
                'errors' => [],
                'data' => [],
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public static function not_found($message = 'Not Found', $errors = []): JsonResponse
    {
        return response()->json(
            [
                'status' => false,
                'message' => $message,
                'errors' => $errors,
                'data' => [],
                'others' => [],
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    public static function throttled(int $max_attempts = 60, int $retry_after = 60): JsonResponse
    {
        return response()->json(
            [
                'status' => false,
                'message' => 'Too many attemps, please slow down the request.',
                'retry_after' => $retry_after,
                'max_attempts' => $max_attempts,
            ],
            Response::HTTP_TOO_MANY_REQUESTS
        );
    }

    public static function warning($message = 'Warning', $errors = [], $data = [], $others = [], $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json(
            [
                'status' => false,
                'message' => $message,
                'errors' => $errors,
                'data' => $data,
                'others' => $others,
            ],
            $code
        );
    }

    public static function forbidden($message = 'Warning', $errors = [], $data = [], $others = [], $code = Response::HTTP_FORBIDDEN): JsonResponse
    {
        return response()->json(
            [
                'status' => false,
                'message' => $message,
                'errors' => $errors,
                'data' => $data,
                'others' => $others,
            ],
            $code
        );
    }

    public static function badRequest($message = 'Warning', $errors = [], $data = [], $others = [], $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(
            [
                'status' => false,
                'message' => $message,
                'errors' => $errors,
                'data' => $data,
                'others' => $others,
            ],
            $code
        );
    }
}
