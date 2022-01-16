<?php

namespace App\Traits;

trait ApiResponser 
{
    private function successReponse($data, $message, $code = 200)
    {
        return response()->json(array('data' => $data, 'message' => $message), $code);
    }

    private function errorReponse($message, $code)
    {
        return response()->json(array('message' =>$message), $code);
    }
}