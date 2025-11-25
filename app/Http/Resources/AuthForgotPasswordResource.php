<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Password;

class AuthForgotPasswordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'success' => $this->status === Password::RESET_LINK_SENT,
            'status'  => $this->status,
            'message' => $this->message($this->status),
            'email'   => $this->email,
        ];
    }

    private function message($status)
    {
        return match ($status) {
            Password::RESET_LINK_SENT => 'Password reset link sent successfully.',
            Password::INVALID_USER    => 'Email not found in system.',
            default                   => 'Unable to send reset link.',
        };
    }
}
