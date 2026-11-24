<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
$perluGanti = (bool) $user->wajib_ganti_password;

$user->update([
    'password' => Hash::make($validated['password']),
    'wajib_ganti_password' => false,
]);

if ($perluGanti && $user->role === 'guide') {
    return redirect()->route('guide.dashboard')
        ->with('success', 'Password berhasil diganti. Sekarang pilih paket yang kamu tangani dan isi jadwal layananmu.');
}

return back()->with('status', 'password-updated');
    }
}
