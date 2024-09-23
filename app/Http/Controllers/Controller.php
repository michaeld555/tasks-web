<?php

namespace App\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Controller
{

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($data)
    {

        $response = [
            'code' => 200,
            'success' => true,
            'data' => $data,
        ];

        return response()->json($response, 200);

    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($code, $error, $errorMessages = [])
    {

        $response = [
            'code' => $code,
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        throw new HttpResponseException(response()->json($response, $code));

    }

}
