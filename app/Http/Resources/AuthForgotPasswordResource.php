<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthForgotPasswordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'email'  => $this['email'] ?? null,   // array key access
            'status' => $this['status'] ?? null,
        ];
    }
}
