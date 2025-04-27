<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        // No middleware in constructor
    }

    public function welcome()
    {
        return response()->json([
            'message' => 'Welcome to the API',
            'status' => 'success'
        ]);
    }

    // Register a new user
    public function register(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|unique:users,user_id',
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', array_keys(Config::get('permissions.roles'))),
        ]);

        $user = User::create([
            'user_id' => $validated['user_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password_hash' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_email_verified' => false,
            'permissions' => []
        ]);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'User registered successfully. Please check your email for verification link.',
            'data' => $user
        ], 201);
    }

    // Login user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
            return response()->json([
                'message' => 'Access Denied: Invalid email or password',
                'error' => 'authentication_failed'
            ], 401);
        }

        if (!$user->is_email_verified) {
            return response()->json([
                'message' => 'Access Denied: Please verify your email before logging in',
                'error' => 'email_not_verified'
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    // Logout user
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    // Verify email
    public function verifyEmail(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Invalid verification link',
                'error' => 'invalid_verification'
            ], 400);
        }

        if ($user->is_email_verified) {
            return view('auth.already-verified', [
                'data' => $user
            ]);
        }

        $user->is_email_verified = true;
        $user->save();

        return view('auth.verify-email', [
            'data' => $user
        ]);
    }

    // Resend verification email
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->is_email_verified) {
            return response()->json([
                'message' => 'Email already verified',
                'error' => 'already_verified'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link sent successfully'
        ]);
    }

    // Forgot password
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $user = User::where('email', $request->email)->first();

            // Generate password reset token
            $token = Str::random(64);

            // Store token in password_resets table
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]);

            // Send password reset email using Mailtrap
            Mail::send('emails.forgot-password', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Reset Password Notification');
            });

            return response()->json([
                'message' => 'Password reset link sent to your email'
            ]);
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $resetData = DB::table('password_resets')
                ->where([
                    'email' => $request->email,
                    'token' => $request->token
                ])->first();

            if (!$resetData) {
                return response()->json([
                    'message' => 'Invalid token!',
                    'error' => 'invalid_token'
                ], 400);
            }

            // Check if token is expired (60 minutes)
            $createdAt = \Carbon\Carbon::parse($resetData->created_at);
            if ($createdAt->addMinutes(60)->isPast()) {
                DB::table('password_resets')->where('token', $request->token)->delete();
                return response()->json([
                    'message' => 'This password reset token has expired. Please request a new one.',
                    'error' => 'token_expired'
                ], 400);
            }

            $user = User::where('email', $request->email)->update([
                'password_hash' => Hash::make($request->password)
            ]);

            DB::table('password_resets')->where(['email' => $request->email])->delete();

            return response()->json([
                'message' => 'Your password has been changed!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show password reset form
    public function showResetPasswordForm(Request $request, $token = null)
    {
        if (!$token) {
            return redirect('/')->with('error', 'Invalid password reset link.');
        }

        $resetData = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        if (!$resetData) {
            return redirect('/')->with('error', 'This password reset token is invalid or has expired.');
        }

        // Check if token is expired (60 minutes)
        $createdAt = \Carbon\Carbon::parse($resetData->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_resets')->where('token', $token)->delete();
            return redirect('/')->with('error', 'This password reset token has expired. Please request a new one.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $resetData->email
        ]);
    }

    // Welcome endpoint
}