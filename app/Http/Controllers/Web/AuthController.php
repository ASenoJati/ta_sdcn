<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showForm()
    {
        if (Auth::check()) {

            $user = Auth::user();

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('teacher')) {
                return redirect()->route('teacher.dashboard');
            } elseif ($user->hasRole('staff')) {
                return redirect()->route('staff.dashboard');
            }

            return redirect('/dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // 1. Cek apakah role diizinkan
            if ($user->hasRole('teacher') || $user->hasRole('staff')) {
                // Logout kembali agar session login dibatalkan
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Dashboard hanya tersedia untuk administrator',
                ])->onlyInput('email');
            }

            // 2. Jika validasi lolos, barulah regenerate session & redirect
            $request->session()->regenerate();

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ])->onlyInput('email');
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::attempt($credentials, $request->remember)) {
    //         $request->session()->regenerate();

    //         $user = Auth::user();

    //         if ($user->hasRole('admin')) {
    //             return redirect()->route('admin.dashboard');
    //         } elseif ($user->hasRole('teacher')) {
    //             return redirect()->route('teacher.dashboard');
    //         } elseif ($user->hasRole('staff')) {
    //             return redirect()->route('staff.dashboard');
    //         }

    //         return redirect('/dashboard');
    //     }

    //     return back()->withErrors([
    //         'email' => 'Email atau password salah!',
    //     ])->onlyInput('email');
    // }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('info', 'Anda telah berhasil logout.');
    }
}
