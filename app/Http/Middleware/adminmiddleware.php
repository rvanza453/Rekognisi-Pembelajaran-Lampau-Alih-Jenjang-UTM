<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class adminmiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure session is started
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('adminmiddleware: User not authenticated', [
                'session_id' => session()->getId(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Check if user has admin role
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'super_admin') {
            Log::warning('adminmiddleware: Unauthorized role', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
                'expected_role' => 'admin or super_admin',
                'session_id' => session()->getId(),
                'ip' => $request->ip()
            ]);
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('error', 'Unauthorized access');
        }

        // Regenerate session ID periodically
        if (!$request->session()->has('last_session_regeneration') || 
            now()->diffInMinutes($request->session()->get('last_session_regeneration')) > 30) {
            $request->session()->regenerate();
            $request->session()->put('last_session_regeneration', now());
        }

        // Add user info to session
        $request->session()->put('user_id', Auth::id());
        $request->session()->put('user_role', Auth::user()->role);

        Log::info('adminmiddleware: Access granted', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role,
            'session_id' => session()->getId(),
            'ip' => $request->ip()
        ]);

        return $next($request);
    }
} 