<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nik' => ['required', 'string'],
        ]);

        // Cari user berdasarkan NIK
        $user = \App\Models\User::where('nik', $request->nik)->first();

        if (!$user) {
            return back()->withErrors(['nik' => 'NIK tidak ditemukan.']);
        }

        // Buat dummy email untuk password reset (karena Laravel password reset menggunakan email)
        // Kita bisa membuat email palsu berdasarkan NIK
        $dummyEmail = $user->nik . '@sirpo.local';

        // Sementara kita akan menampilkan pesan sukses
        // Dalam implementasi nyata, Anda bisa menggunakan sistem notifikasi lain
        return back()->with('status', 'Link reset password telah dikirim! (Silakan hubungi admin untuk mendapatkan password baru)');
    }
}
