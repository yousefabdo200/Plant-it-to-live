<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class CroprecommendationController extends Controller
{
    use ApiResponse;
    public function __construct()
    {
        //$this->middleware('auth:user');
    }
    public function sendRequestToCropRecommendation(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'n' => 'required|numeric',
            'pho' => 'required|numeric',
            'po' => 'required|numeric',
            'T' => 'required|numeric',
            'PH' => 'required|numeric',
            'H' => 'required|numeric',
            'R' => 'required|numeric',
        ]);
        if ($validator->fails()) {
           // return $this->response($validator->errors(), 'Validation errors', 406);
           return $this->validationerrors($validator->errors());
        }
        $n = $request->input('n');
        $pho = $request->input('pho');
        $po=$request->input('po');
        $T=$request->input('T');
        $PH=$request->input('PH');
        $H=$request->input('H');
        $R=$request->input('R');
        $data = [
            'n' => $n,
            'pho' => $pho,
            'po'=>$po,
            'T'=>$T,
            'PH'=>$PH,
            'H'=>$H,
            'R'=>$R
        ];
        // Send POST request to Flask endpoint
        $response = Http::post('http://localhost:5000/predict', $data);
        // Check if request was successful
        if ($response->successful()) {
            $responseData = $response->json(); // Get response data
        // Access data from the response
            $prediction = $responseData['prediction']; // Assuming 'prediction' is returned from Flask
            // Do something with the prediction
            // For example, return it as part of your response
            return $this->SuccessResponse($prediction, "Prediction received");
        } else {
            // Handle error
            return $this->failed("Model Not working");//response()->json(['error' => 'Failed to communicate with Flask server'], $response->status());
        }
    }
}
