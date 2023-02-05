<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    use hasApiTokens;

    public function register(Request $request)
    {
        if($request->nickname == null || $request->nickname == '') {

            $request->merge(['nickname' => 'Anonymous']);
        }

        $validatedData = $request->validate([
            'nickname' => 'nullable|max:45',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);
    
        $validatedData['password'] = Hash::make($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([
            'message' => 'User registered succesfully',
            'user' => $user,   
            'access_token' => $accessToken
        ],201);
    }
    
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required',
          ]);

        if(!auth()->attempt($loginData)) {
            return response (['message' => 'Invalid email or password']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response([
            'user' => auth()->user(),
            'access_token' => $accessToken
        ]);
    }

    public function logout(Request $request) {

        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
   }
}
