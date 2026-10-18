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
    public function create(): View
    {
        return view('auth.login', ['mode' => 'login']);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $portal  = $request->input('portal') === 'admin' ? 'admin' : 'customer';
        $isAdmin = $request->user()->role === 'admin';

        if ($portal === 'admin' && ! $isAdmin) {
            Auth::guard('web')->logout();

            return back()
                ->withInput($request->only('email', 'portal', 'form'))
                ->withErrors(['email' => 'Akun ini bukan akun admin. Pilih "Customer" untuk masuk.']);
        }

        if ($portal === 'customer' && $isAdmin) {
            Auth::guard('web')->logout();

            return back()
                ->withInput($request->only('email', 'portal', 'form'))
                ->withErrors(['email' => 'Ini akun admin. Pilih "Admin" untuk masuk.']);
        }

        $request->session()->regenerate();

        return $isAdmin
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}