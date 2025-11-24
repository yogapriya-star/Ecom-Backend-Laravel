<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\AuthForgotPasswordResource;
use App\Http\Resources\AuthResetPasswordResource;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return new AuthForgotPasswordResource([
            'email' => $request->email,
            'status' => $status
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => bcrypt($request->password),
                    'confirm_password' => $request->password,
                ])->save();
            }
        );

        return new AuthResetPasswordResource(['status' => $status]);
    }
}
