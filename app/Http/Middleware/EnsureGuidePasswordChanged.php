<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuidePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'guide' && $user->wajib_ganti_password) {
            return redirect()->route('profile.edit')
                ->with('wajib_ganti', 'Demi keamanan, ganti password sementaramu dulu sebelum memakai dashboard guide.');
        }

        return $next($request);
    }
}