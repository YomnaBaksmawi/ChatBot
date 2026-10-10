<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
         $data=$request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' =>'USER',
            
        ]);

        return new UserResource($user);
    

    }
        public function login(LoginRequest $request):JsonResponse
    {
        $credentials = $request->validated();

    if (! $token = auth('api')->attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid email or password',
        ], 401);
    }

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => new UserResource(auth('api')->user()),
    ]);
    }
    public function logout(): JsonResponse
{
    auth('api')->logout();

    return response()->json([
        'message' => 'Logout successful',
    ]);
}
}
