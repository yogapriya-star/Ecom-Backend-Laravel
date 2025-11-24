<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\AuthForgotPasswordResource;
use App\Http\Resources\AuthResetPasswordResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Forgot password
    */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        // Pass array to Resource
        return new AuthForgotPasswordResource([
            'email'  => $request->email,
            'status' => $status
        ]);
    }

    /**
     * Reset password
    */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return new AuthResetPasswordResource([
            'status' => $status
        ]);
    }
}
