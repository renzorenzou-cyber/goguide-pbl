<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /** Kirim password sementara ke email wisatawan atau guide. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Admin tidak ikut: password admin dikelola lewat command CreateAdmin.
        $user = User::where('email', $request->email)
            ->where('role', '!=', 'admin')
            ->first();

        if ($user) {
            $passwordBaru = Str::random(10);

            $isi = "Halo {$user->name},\n\n"
                . "Kamu meminta password baru untuk akun Go Guide.\n\n"
                . "Login di: " . route('login') . "\n"
                . ($user->username ? "Username: {$user->username}\n" : '')
                . "Email: {$user->email}\n"
                . "Password sementara: {$passwordBaru}\n\n"
                . "Setelah login, segera ganti password di menu Profil.\n"
                . "Jika kamu tidak merasa meminta ini, abaikan email ini lalu hubungi admin.\n\n"
                . "Salam,\nGo Guide";

            try {
                // Kirim email dulu. Password baru disimpan hanya bila email berhasil terkirim,
                // supaya pengguna tidak terkunci dari akunnya sendiri.
                Mail::raw($isi, fn ($m) => $m->to($user->email)->subject('Password sementara Go Guide'));

                $user->update([
                    'password'             => Hash::make($passwordBaru),
                    'wajib_ganti_password' => true,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Email password sementara gagal dikirim: ' . $e->getMessage(), ['email' => $user->email]);

                return back()->withInput()->withErrors([
                    'email' => 'Email gagal dikirim. Coba lagi beberapa saat lagi.',
                ]);
            }
        }

        // Pesan sama baik email terdaftar maupun tidak, supaya orang lain tidak bisa menebak email yang terdaftar.
        return back()->with('status', 'Jika email terdaftar, password sementara sudah dikirim. Cek inbox atau folder spam.');
    }
}