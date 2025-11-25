<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Password;

class AuthResetPasswordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'success' => $this->status === Password::PASSWORD_RESET,
            'status'  => $this->status,
            'message' => $this->message($this->status),
        ];
    }

    private function message($status)
    {
        return match ($status) {
            Password::PASSWORD_RESET => 'Password reset successfully.',
            Password::INVALID_TOKEN  => 'Invalid or expired token.',
            Password::INVALID_USER   => 'No user found with this email.',
            default                  => 'Unable to reset password.',
        };
    }
}
