<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthForgotPasswordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT,
            'message' => __($this->status),
            'data' => ['email' => $this->email]
        ];
    }
}
