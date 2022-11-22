<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return send_error('Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $request['password'] = Hash::make($request['password']);
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'userData' => $user,
        ];
        return send_response('Registration Successful.', $data, Response::HTTP_CREATED);
    }
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'userData' => $user,
        ];

        return send_response('Login Successful.', $data, Response::HTTP_CREATED);
    }
    public function me(Request $request)
    {
        $data = [
            'userData' => $request->user()
        ];

        return send_response('Data Retrive Successful', $data, Response::HTTP_CREATED);
    }
    public function logout(Request $request)
    {
        $user = request()->user(); //or Auth::user()

        // Revoke current user token
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        $data = [
            'userData' => $user,
        ];

        return send_response('You Are Successful Logout.', $data, Response::HTTP_CREATED);
    }
}
