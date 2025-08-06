<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class assessormiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('login')->with('error', 'Please login first');
        }

        if (Auth::user()->role !== 'assessor') {
            return redirect('login')->with('error', 'Unauthorized access');
        }

                return $next($request);
    }
} 