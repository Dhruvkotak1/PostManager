<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($msg, $data)
    {
        $response = [
            'status' => true,
            'msg' => $msg,
            'data' => $data,
            'user' => auth()->user()->id
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessage = [], $code = 404)
    {
        $response = [
            'status' => false,
            'msg' => $error
        ];
        if (!empty($errorMessage)) {
            $response['data'] = $errorMessage;
        }
        
        return response()->json($response, $code);
    }
}
