<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthRegisterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => true,
            'message' => 'Registered successfully',
            'token'   => $this['token'],
            'token_type' => 'Bearer',

            'data' => [
                'id' => $this['user']->id,
                'full_name' => $this['user']->full_name,
                'email' => $this['user']->email,
                'phone' => $this['user']->phone,
                'role' => $this['user']->role,
            ]
        ];
    }
}
