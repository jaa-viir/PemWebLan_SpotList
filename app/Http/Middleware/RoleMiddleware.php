<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        // Jika user tidak login atau rolenya tidak ada dalam daftar yang diizinkan, tolak akses
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, hanya Admin yang boleh mengelola panduan SpotList.'
            ], 403); // 403 Forbidden
        }

        return $next($request);
    }
}
