<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResetPasswordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status' => $this['status'] ?? null,
        ];
    }
}
