<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class logincontroller extends Controller
{
    public function login(){
        return view('/login');
    }
    public function loginproses(Request $request)
    {
        \Log::info('Login attempt', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if(Auth::attempt($credentials, true))
        {
                \Log::info('Login successful', [
                    'user_id' => Auth::id(),
                    'user_role' => Auth::user()->role
                ]);

                $request->session()->regenerate();
                
            if(Auth::User()->role == 'pendaftar')
            {
                // Cek apakah user adalah mahasiswa alihjenjang
                if (!Auth::User()->canAccessSystem()) {
                    // Redirect ke halaman khusus untuk mahasiswa eporto
                    return redirect('/redirect-eporto');
                }
                return redirect()->intended('user/dashboard');
            }
            else if(Auth::User()->role == 'assessor')
            {
                return redirect()->intended('assessor/dashboard');
            }
            else if(Auth::User()->role == 'admin')
            {
                return redirect()->intended('admin/dashboard');
            }
                else if(Auth::User()->role == 'super_admin')
            {
                    \Log::info('Super admin login successful, redirecting to dashboard');
                    return redirect()->intended('super/dashboard');
                }
                else
                {
                    \Log::warning('Invalid role detected', [
                        'user_id' => Auth::id(),
                        'role' => Auth::User()->role
                    ]);
                    Auth::logout();
                    return redirect('login')->with('error','Role tidak valid');
                }
            }
            else
            {
                \Log::warning('Login failed', [
                    'email' => $request->email,
                    'ip' => $request->ip()
                ]);
                return redirect()->back()->with('error', 'Email atau password salah');
            }
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
        }
    }

    public function register(){
        return view('register');
    }

    public function registeruser(Request $request){
        user::create([
            'email' => $request->email,
            'username' =>$request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'remember_token' => Str::random(60),
        ]);
        return redirect('/login');
    }
    public function logout(Request $request)
    {
        \Log::info('User logging out', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user() ? Auth::user()->role : 'not authenticated'
        ]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function redirectEporto()
    {
        return view('redirect-eporto');
    }
}
