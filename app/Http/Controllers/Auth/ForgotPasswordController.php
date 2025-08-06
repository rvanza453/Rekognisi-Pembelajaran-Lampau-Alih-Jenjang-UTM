<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $token = Str::random(64);
            
            // Hapus token lama
            \DB::table('password_resets')
                ->where('email', $request->email)
                ->delete();
            
            // tambahkan token baru
            \DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]);

            Log::info('Attempting to send reset password email', [
                'email' => $request->email,
                'token' => $token,
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'username' => config('mail.mailers.smtp.username'),
                ]
            ]);

            // Mengirim email
            Mail::to($request->email)->send(new ResetPasswordMail($token));

            Log::info('Reset password email sent successfully', [
                'email' => $request->email,
                'token' => $token
            ]);

            return back()->with('status', 'We have emailed your password reset link! Please check your email (including spam folder).');
        } catch (Exception $e) {
            Log::error('Failed to send reset password email', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'username' => config('mail.mailers.smtp.username'),
                ]
            ]);

            return back()->withErrors(['email' => 'We could not send the reset password email. Please try again later or contact support.']);
        }
    }
} 