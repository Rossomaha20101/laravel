<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthForest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('forest')->check()) {
            return redirect()->route('register'); // или login для forest
        }

        return $next($request);
    }
}