<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    private const LABELS = [
        'customer' => 'Customer',
        'guide'    => 'Guide',
        'admin'    => 'Admin',
    ];

    public function create(): View
    {
        return view('auth.login', ['mode' => 'login']);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $chosen = in_array($request->input('portal'), ['admin', 'guide'], true)
            ? $request->input('portal')
            : 'customer';

        $role   = $request->user()->role;
        $actual = in_array($role, ['admin', 'guide'], true) ? $role : 'customer';

        // Pilihan di form harus cocok dengan role akun
        if ($chosen !== $actual) {
            Auth::guard('web')->logout();

            return back()
                ->withInput($request->only('email', 'portal', 'form'))
                ->withErrors([
                    'email' => 'Akun ini terdaftar sebagai ' . self::LABELS[$actual]
                        . '. Pilih "' . self::LABELS[$actual] . '" untuk masuk.',
                ]);
        }

        $request->session()->regenerate();

        return match ($actual) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'guide' => redirect()->intended(route('guide.dashboard')),
            default => redirect()->intended(route('dashboard')),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}