<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class ApiResponse extends Controller
{
    public function publicSendResponse($code,$message ,$total, $data): Response|Application|ResponseFactory
    {
        if ($code == 200 || $code == 201) {
            return response([
                'status' => 'success',
                'message' => $message,
                'code' => $code,
                'total' => $total,
                'data' => $data
            ], $code);
        } else {
            return response([
                'status' => 'error',
                'message' => $message,
                'code' => $code,
                'total' => $total,
                'data' => $data
            ], $code);
        }
    }

    public function adminSendResponse($code, $message, $data): Response|Application|ResponseFactory
    {
        if ($code == 200 || $code == 201) {
            return response([
                'status' => 'success',
                'code' => $code,
                'message' => $message,
                'data' => $data
            ], $code);
        } else {
            return response([
                'status' => 'error',
                'code' => $code,
                'message' => $message,
                'data' => $data
            ], $code);
        }
    }

}
