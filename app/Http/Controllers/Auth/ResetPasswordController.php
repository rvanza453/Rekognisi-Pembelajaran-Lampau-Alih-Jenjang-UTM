<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function showResetForm($token)
    {
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        if (!$reset || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return redirect()->route('password.request')
                ->with('error', 'This password reset token is invalid or has expired.');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $reset->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return back()->withInput($request->only('email'))
                ->with('error', 'This password reset token is invalid or has expired.');
        }

        DB::table('users')
            ->where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully!');
    }
} 