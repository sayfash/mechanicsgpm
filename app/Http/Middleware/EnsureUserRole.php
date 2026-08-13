<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            return response()->json([
                'error' => 'Forbidden. Access restricted to roles: ' . implode(', ', $roles)
            ], 403);
        }

        return $next($request);
    }
}
