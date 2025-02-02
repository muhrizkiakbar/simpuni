<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class KonsultanPetugasMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if the user is logged in and has the correct role
        if (!$user || ($user->type_user !== 'petugas' && $user->type_user !== 'konsultan')) {
            abort(403, 'You must be an petugas to access this resource.');
        }
        return $next($request);
    }
}
