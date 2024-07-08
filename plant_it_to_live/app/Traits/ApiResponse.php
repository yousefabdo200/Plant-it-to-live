<?php

namespace App\Traits;

trait ApiResponse
{
    //Sucess data
    public function SuccessResponse($data=null,$msg=null)
    {
        if($msg===null)
        $msg="Done";
        return response()->json(
            [
                'sucess'=>true,
                'message'=>$msg,
                'data'=>$data,
            ],200);
    }
    //validation Errors
    public function validationerrors($data=null,$msg=null)
    {
        if($msg===null)
        $msg="Validation error Try agian please ";
        return response()->json(
            [
                'sucess'=>false,
                'message'=>$msg,
                'data'=>$data,
            ],422);
    }
    //login errors

    //failedResponses
    public function failed($msg=null)
    {
        if($msg===null)
            $msg="Try agian please ";
        return response()->json(
            [
                'sucess'=>false,
                'message'=>$msg,
            ],404);
    }
}
