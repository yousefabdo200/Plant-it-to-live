<?php

namespace App\Http\Controllers;

use validation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class DiseasesDetectionController extends Controller
{
    use ApiResponse;
    public function __construct()
    {
       //$this->middleware('auth:user');
    }
    public function sendRequestToDiseasesDetection(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
        if ($validator->fails()) {
           // return $this->response($validator->errors(), 'Validation errors', 406);
           return $this->validationerrors($validator->errors());
        }
      $img=$request->file('img');

      $file=time().'_'.rand(1,100).'.'.$img->extension();
      $img->move(public_path("palntsDieseases"),$file);
        $path="C:\\xampp\htdocs\plant-it-to-live\backend\plant_it_to_live\public\palntsDieseases"."\\".$file;
      //$path=url('/')."/palntsDieseases"."/".$file;
        $data = [
           'image_path'=>$path
        ];
        // Send POST request to Flask endpoint
        $response = Http::timeout(60)->post('http://localhost:5000/detect', $data);
        // Check if request was successful
        unlink($path);
        if ($response->successful()) {
            $responseData = $response->json(); // Get response data
        // Access data from the response

            return $this->SuccessResponse($responseData, "Prediction received");
        } else {
            // Handle error
            return $this->failed("Model Not working");//response()->json(['error' => 'Failed to communicate with Flask server'], $response->status());
        }
    }
}
