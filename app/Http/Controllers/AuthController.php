<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

use Tymon\JWTAuth\Exceptions\JWTException;

use App\Models\User;
use App\Mail\VerifyEmail;
use Carbon\Carbon;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;

use App\Http\Resources\AuthResetPasswordResource;
use App\Http\Resources\AuthRegisterResource;
use App\Http\Resources\AuthLoginResource;
use App\Http\Resources\AuthForgotPasswordResource;
use App\Http\Resources\AuthResendVerificationResource;


class AuthController extends Controller
{
    /**
     * Register
    */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'confirm_password' => $request->confirm_password,
            'role' => 'customer',
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        // Generate signed verification link (valid for 60 minutes)
        $verifyUrl = URL::temporarySignedRoute(
            'verify.email',
              Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Send verification email
        Mail::to($user->email)->send(new VerifyEmail($user, $verifyUrl));

        return new AuthRegisterResource([
            'user'  => $user,
            'token' => $token
        ]);
    }

    /**
     * Login
    */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        // Email verification
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Email not verified. Please verify your email to log in.'
            ], 403);
        }

        try {
            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            // Update last login
            $user->last_login_at = now();
            $user->save();

            // Return Resource
            return new AuthLoginResource([
                'user'  => $user,
                'token' => $token
            ]);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not create token.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Email
    */
    public function verifyEmail(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or Expired Verification Link'
            ], 400);
        }

         // Find user by ID
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Check if already verified
        if ($user->email_verified_at) {
            return response()->json([
                'status' => true,
                'message' => 'Email already verified.'
            ]);
        }

        // Mark email as verified
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully!'
        ]);
    }

    /**
     * Resend verification email
    */
    public function resendVerificationByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Email already verified.'
            ], 409);
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verify.email',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );

        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

        return response()->json([
            'status' => true,
            'message' => 'Verification email sent successfully.',
            'verification_url' => $verificationUrl
        ]);
    }


    /**
     * Logout 
    */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());
            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully.'
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not log out.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}