<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan formulir login terpadu multi-role TemJi.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDefaultRedirectRoute());
        }

        return view('auth.login');
    }

    /**
     * Proses otentikasi login pengguna dengan deteksi peran otomatis.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            return redirect()
                ->intended(route($user->getDefaultRedirectRoute()))
                ->with('success', "Selamat datang kembali, {$user->name}! Anda berhasil masuk sebagai {$user->role_label}.");
        }

        return back()
            ->withErrors([
                'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai dengan data kami.',
            ])
            ->onlyInput('email');
    }

    /**
     * Akhiri sesi login pengguna dan batalkan token sesi.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda telah berhasil keluar dari akun TemJi.');
    }
}
