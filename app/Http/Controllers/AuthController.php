<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    // Login
    public function authLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'url' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        if (User::where('email', $request->email)->count() == 0) {
            return response()->json([
                'error' => 'Email atau password salah.',
            ], 401);
        }

        if (Auth::attempt($credentials)) {
            session()->put('success', 'Login berhasil!');
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect('/admin' . $request->input('url'));
            } elseif (Auth::user()->role === 'admin-master') {
                return redirect('/admin-master' . $request->input('url'));
            }
        }

        session()->put('error', 'Email atau password salah.');

        return Redirect::back();
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('umum.dashboard')->with('success', 'Logout berhasil!');
    }
}
