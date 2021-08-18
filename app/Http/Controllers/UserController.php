<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register (UserRegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('token')->plainTextToken;

        return $response = [
            'user' => new UserResource($user),
            'token' => $token
        ];
    }

    public function login (UserLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response([
                'message' => "Bad creds"
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        return $response = [
            'user' => new UserResource($user),
            'token' => $token
        ];
    }

    public function logout ()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logget out'
        ];
    }

    public function show (User $user)
    {
        return new UserResource($user);
    }
}
