<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthLoginResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $this['token'],
            'token_type' => 'Bearer',

            'user' => [
                'id'    => $this['user']->id,
                'name'  => $this['user']->name,
                'email' => $this['user']->email,
                'role'  => $this['user']->role,
            ]
        ];
    }
}
