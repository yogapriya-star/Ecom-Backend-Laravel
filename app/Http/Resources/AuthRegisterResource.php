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
            'message' => 'User registered successfully',
            'data' => [
                'id' => $this->id,
                'full_name' => $this->full_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
            ]
        ];
    }
}
