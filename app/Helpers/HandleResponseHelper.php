<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

/***
 * 200 OK
 * 400 Bad Request
 * 401 Unauthorized
 * 404 Not Found
 */

function handleAuthResponse($request, $responseMessage, $page, $statusCode, $data = null)
{
    $data   = auth('sanctum')->user();
    $token  = $data->createToken('auth_token')->plainTextToken;    
    $result = [
        'name'  => $data->name,
        'role'  => DB::table("roles")->where("id", $data->role)->first(),
        'access_token' => $token
    ];

    if ($request->expectsJson()) {
        return response()->json([
                    'status' => true,
                    'message'=> $responseMessage,
                    'code'   => $statusCode,
                    'results'=> $result
                ], 200);
    } else {
        Session::put($result);

        return redirect()->to($page);
    }
}   

function handleErrorResponse($request, $responseMessage, $page, $statusCode, $data = null)
{
    if ($request->expectsJson()) {
        return response()->json(['message' => $responseMessage, 'data' => $data], $statusCode);
    } else {
        Session::put('error', $responseMessage);
        return redirect()->to($page);
    }
}

function handleApiResponse()
{}