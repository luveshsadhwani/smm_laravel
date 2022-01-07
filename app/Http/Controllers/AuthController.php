<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // input gets values from form data
        $email = $request->input('email');
        $password = $request->input('password');

        $credentials = array(
            'email' => $email,
            'password' => $password
        );

        // use auth to check user credentials, we can't get the password directly since it is hidden
        if(!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Incorrect email/password', 
                'data' => []
            ], 401);
        }

        $user = Auth::user();
            
        // create token for user, the createToken method is available because of the HasApiTokens trait
        $token = $user->createToken('smm')->plainTextToken;

        return response()->json([
            'message' => 'Successful login', 
            'data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'token' => $token
            ]
        ], 200);

    }

    public function logout()
    {
        // get user and revoke the token used to log in
        $user = Auth::user();

        if (empty($user)){
            return "Invalid request";
        }

        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Successful logout', 
            'data' => []
        ], 200);
    }

    public function register(Request $request)
    {
        // input gets values from form data
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $password = $request->input('password');

        // Create new user instance, update model and save to DB
        $user = new User();
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();

        return response()->json([
            'message' => 'Registration successfull', 
            'data' => []
        ], 201);
    }
}
