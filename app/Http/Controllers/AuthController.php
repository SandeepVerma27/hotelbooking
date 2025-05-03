<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {

            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role' => 'nullable|in:admin,user'
            ]);

            /* Check for validation errors */
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            /* Check if the user already exists */
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'User already exists'
                ], 409);
            }

            /* Create a new user */
            $data = $validator->validated();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'user',
                'created_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {

            /* Validate credentials */
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $validator->validated();

            /* Check if the user exists */
            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            $user = auth()->user();
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
