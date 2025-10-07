<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getToken', 'register']]);
    }

    /**
     * Get access token for API access
     */
    public function getToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid input',
                'messages' => $validator->errors()
            ], 400);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not create token'
            ], 500);
        }

        $client = JWTAuth::user();

        return response()->json([
            'message' => 'Token created successfully',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'branch_id' => $client->branch_id
                ]
            ]
        ]);
    }

    /**
     * Register a new client (for testing purposes)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:MALE,FEMALE',
            'branch_id' => 'required|exists:branches,id',
            'registration_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid input',
                'messages' => $validator->errors()
            ], 400);
        }

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'gender' => $request->gender,
            'branch_id' => $request->branch_id,
            'registration_date' => $request->registration_date,
        ]);

        $token = JWTAuth::fromUser($client);

        return response()->json([
            'message' => 'Client registered successfully',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'branch_id' => $client->branch_id
                ]
            ]
        ], 201);
    }

    /**
     * Get authenticated client profile
     */
    public function me()
    {
        $client = auth()->user();

        return response()->json([
            'data' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'gender' => $client->gender,
                'branch_id' => $client->branch_id,
                'registration_date' => $client->registration_date,
                'branch' => $client->branch,
                'loans_count' => $client->loans()->count(),
                'active_loans_count' => $client->loans()->where('status', 'ACTIVE')->count()
            ]
        ]);
    }

    /**
     * Refresh JWT token
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();
            return response()->json([
                'message' => 'Token refreshed successfully',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not refresh token'
            ], 500);
        }
    }

    /**
     * Logout client (invalidate token)
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not logout'
            ], 500);
        }
    }
}
