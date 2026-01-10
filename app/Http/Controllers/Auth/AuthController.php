<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required|min:6',
        ], [
            'login.required' => 'Email atau Username wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('login'));
        }

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->filled('remember');

        // Cek login via email atau username
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Cek apakah user ada dan aktif
        $user = \App\Models\User::where($fieldType, $login)->first();

        if (!$user) {
            return back()
                ->withErrors(['login' => 'Email atau Username tidak terdaftar'])
                ->withInput($request->only('login'));
        }

        if (!$user->is_active) {
            return back()
                ->withErrors(['login' => 'Akun Anda tidak aktif. Hubungi administrator.'])
                ->withInput($request->only('login'));
        }

        // Attempt login
        $credentials = [
            $fieldType => $login,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['password' => 'Password salah'])
            ->withInput($request->only('login'));
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
