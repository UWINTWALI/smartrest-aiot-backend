<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      title="Authentication API",
 *      description="API for user authentication and management",
 *      version="1.0.0"
 * )
 */
class AuthController extends Controller
{
    /**
     * Register a new user account (Patient or Customer).
     *
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user account",
     *     description="Create a new patient or customer account",
     *     operationId="authRegister",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation","role"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Smith"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"patient", "customer"}, example="patient")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration failed"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'required|string|email|max:80|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:patient,customer',
        ]);

        DB::beginTransaction();
        try {
            // Create the user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
            
            // Create profile based on role
            if ($request->role === 'patient') {
                PatientProfile::create([
                    'patient_id' => $user->user_id
                ]);
            }

            DB::commit();
            
            event(new Registered($user));
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verify email address via token.
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ]);
        }

        $request->fulfill();
        
        return response()->json([
            'message' => 'Email verified successfully'
        ]);
    }
      /**
     * Login user and issue JWT + refresh tokens.
     *
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Log in a user",
     *     description="Authenticate user and issue access token",
     *     operationId="authLogin",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }
    
    /**
     * Logout user (revoke tokens).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
    
    /**
     * Get authenticated user profile.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        // Load the appropriate profile based on user role
        if ($user->isPatient()) {
            $user->load('patientProfile');
        } elseif ($user->isDoctor()) {
            $user->load('doctorProfile');
        }
        
        return response()->json($user);
    }
    
    /**
     * Refresh token.
     */
    public function refresh(Request $request)
    {
        // In Sanctum, we don't have refresh tokens, so we'll just create a new one
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $token,
        ]);
    }
    
    /**
     * Send password reset link.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $status = Password::sendResetLink(
            $request->only('email')
        );
        
        return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => 'Reset link sent to your email'])
                : response()->json(['message' => 'Unable to send reset link'], 400);
    }
    
    /**
     * Reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );
        
        return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => 'Password has been reset'])
                : response()->json(['message' => 'Unable to reset password'], 400);
    }
    
    /**
     * Change password while logged in.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = $request->user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 400);
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
    
    /**
     * Social login (OAuth).
     */
    public function socialLogin(Request $request)
    {
        // This would be implemented with Laravel Socialite
        return response()->json([
            'message' => 'Social login is not implemented yet'
        ], 501);
    }
}
