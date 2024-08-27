<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function sendResponse($result, $message = 'success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $message,
            'status' => $code,
        ], $code);
    }

    public function sendError($error, $code = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $error,
            'status' => $code,
        ], $code);
    }
}
