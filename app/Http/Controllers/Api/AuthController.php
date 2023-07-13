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
            'password' => 'required|confirmed|min:8',
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

        $token = $user->createToken('auth_token')->plainTextToken;
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'userData' => $user,
        ];
        return send_response('Logged In Successful.', $data, Response::HTTP_CREATED);
    }
    public function profile(Request $request)
    {
        $data = [
            'userData' => $request->user()
        ];
        return send_response('User Retrieved SuccessFul.', $data, Response::HTTP_FOUND);
    }
    public function logout(Request $request)
    {
        $data = [
            'userData' => $request->user()
        ];
        $request->user()->tokens()->delete();

        return send_response('Logged Out Successful.', $data, Response::HTTP_FOUND);
    }
}
