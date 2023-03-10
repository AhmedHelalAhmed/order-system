<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    /**
     * @param  array  $data
     * @param $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sucessResponse(array $data = [], $status = Response::HTTP_OK)
    {
        return response()->json([
            'data' => $data,
        ], $status);
    }

    /**
     * @param  string  $message
     * @param  array  $errors
     * @param $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = '', array $errors = [], $status = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'errors' => $errors,
            'message' => $message,
        ], $status);
    }
}
