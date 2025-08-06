<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class dashboardcontroller extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('login');
        }

        $data['getRecord'] = User::find($user->id);

        switch ($user->role) {
            case 'pendaftar':
                // Cek apakah user adalah mahasiswa eporto
                if (!Auth::user()->canAccessSystem()) {
                    return redirect('/redirect-eporto');
                }
                return view('User.dashboard');
            case 'assessor':
                return view('Assessor.dashboard', $data);
            case 'admin':
                return view('Admin.dashboard', $data);
            case 'super_admin':
                return view('Super_admin.dashboard', $data);
            default:
                Auth::logout();
                return redirect('login')->with('error', 'Invalid role');
        }
    }
}
