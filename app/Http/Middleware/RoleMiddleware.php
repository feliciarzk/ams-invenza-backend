<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        $userRole = $user->role?->name;

        if (!$userRole || !in_array($userRole, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission',
            ], 403);
        }

        return $next($request);
    }
}