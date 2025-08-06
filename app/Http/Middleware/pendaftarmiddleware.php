<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class pendaftarmiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('login')->with('error', 'Please login first');
        }

        if (Auth::user()->role !== 'pendaftar') {
            // Cek apakah user adalah mahasiswa eporto
                if (!Auth::user()->canAccessSystem()) {
                    return redirect('/redirect-eporto');
                }
            return redirect('login')->with('error', 'Unauthorized access');
        }

                return $next($request);
    }
} 