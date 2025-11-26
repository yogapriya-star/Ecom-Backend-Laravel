<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, $roles)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $allowedRoles = explode('|', $roles);

        if (! in_array($user->role, $allowedRoles)) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return $next($request);
    }
}
