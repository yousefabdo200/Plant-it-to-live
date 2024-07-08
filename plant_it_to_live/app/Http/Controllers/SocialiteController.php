<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Traits\ApiResponse;

class SocialiteController extends Controller
{
    use ApiResponse;
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Retrieve user details from Google
            $user = Socialite::driver('google')->user();

            // Check if the user exists in your database or create a new user
            $existingUser = User::where('email', $user->getEmail())->first();

            if ($existingUser) {
                // If the user exists, log them in
                Auth::login($existingUser);
                $token = JWTAuth::fromUser($existingUser);
            } else {
                // If the user doesn't exist, create a new user
                $newUser = new User();
                $newUser->name = $user->getName();
                $newUser->email = $user->getEmail();
                $newUser->activated = true;
                $newUser->save();

                // Log in the new user
                Auth::login($newUser);
                $token = JWTAuth::fromUser($newUser);
            }

            // Return token to frontend
            //Session::flush();
            return $this->SuccessResponse(['token' => $token]);
        } catch (\Exception $e) {
            // Handle any errors
            return $this->failed("Failed to authenticate with Google");
        }
    }
}
