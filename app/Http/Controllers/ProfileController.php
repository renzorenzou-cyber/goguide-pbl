<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Rapikan nomor HP: buang spasi, strip, kurung
        $request->merge([
            'phone' => preg_replace('/[\s\-().]/', '', (string) $request->input('phone')) ?: null,
        ]);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'regex:/^(\+62|62|0)8[0-9]{8,11}$/'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'email.unique'   => 'Email ini sudah dipakai akun lain.',
            'phone.regex'    => 'Nomor HP tidak valid. Contoh: 081234567890 atau +6281234567890.',
            'photo.image'    => 'File harus berupa gambar.',
            'photo.mimes'    => 'Format foto harus JPG, PNG, atau WebP.',
            'photo.max'      => 'Ukuran foto maksimal 2 MB.',
            'photo.uploaded' => 'Foto gagal diunggah, coba file yang lebih kecil.',
        ]);

        // Hanya kolom ini yang boleh diubah dari form (role tidak pernah ikut)
        $update = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            $this->deletePhoto($user->photo);

            $file     = $request->file('photo');
            $filename = $user->id . '-' . Str::random(10) . '.' . $file->extension();

            File::ensureDirectoryExists(public_path('images/avatars'));
            $file->move(public_path('images/avatars'), $filename);

            $update['photo'] = 'images/avatars/' . $filename;
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($user->photo);
            $update['photo'] = null;
        }

        $user->update($update);

        return back()->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $this->deletePhoto($user->photo);
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Hanya hapus file di folder avatars, jadi foto guide/tour aman
    private function deletePhoto(?string $path): void
    {
        if ($path && str_starts_with($path, 'images/avatars/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}