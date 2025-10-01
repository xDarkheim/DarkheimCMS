<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle login request
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                // Log failed login attempt
                ActivityLog::create([
                    'user_id' => null,
                    'action' => 'login_failed',
                    'description' => "Failed login attempt for email: {$request->email}",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'severity' => 'warning'
                ]);

                return response()->json([
                    'message' => 'Invalid credentials',
                    'errors' => [
                        'email' => ['The provided credentials are incorrect.'],
                    ]
                ], 422);
            }

            // Check if user has admin role
            if ($user->role !== 'admin') {
                // Log unauthorized access attempt
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'unauthorized_access',
                    'description' => "User {$user->email} attempted admin access without privileges",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'severity' => 'critical'
                ]);

                return response()->json([
                    'message' => 'Unauthorized access',
                    'errors' => [
                        'email' => ['You do not have admin privileges.'],
                    ]
                ], 403);
            }

            // Create Sanctum token
            $token = $user->createToken('admin-token')->plainTextToken;

            // Log successful login
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => "User {$user->email} logged in successfully",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => 'info'
            ]);

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Login failed',
                'error' => 'An unexpected error occurred during login'
            ], 500);
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Log logout
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'description' => "User {$user->email} logged out",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => 'info'
            ]);

            // Revoke the current access token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Logout failed',
                'error' => 'An error occurred during logout'
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        } catch (\Exception $e) {
            Log::error('User fetch error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch user data',
                'error' => 'An error occurred while retrieving user information'
            ], 500);
        }
    }

    /**
     * Validate current token
     */
    public function validateToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            return response()->json([
                'valid' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Token validation error: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'message' => 'Token validation failed'
            ], 500);
        }
    }
}
