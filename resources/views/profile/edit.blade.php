@extends('layouts.site')

@section('title', 'Profil Saya - Go Go Guide')

@section('content')
@php
    $isAdmin = $user->role === 'admin';
    $accent  = $isAdmin ? '#0f172a' : '#f26522';
    $headBg  = $isAdmin ? '#0f172a' : 'linear-gradient(135deg, #133961 0%, #1e5288 100%)';
    $backUrl = $isAdmin ? route('admin.dashboard') : route('dashboard');
@endphp

<section class="py-10 sm:py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <a href="{{ $backUrl }}" class="text-sm text-slate-500 hover:text-brand-blue">&larr; Kembali ke Dashboard</a>

        {{-- Header (beda warna untuk admin dan customer) --}}
        <div class="mt-4 rounded-3xl p-6 sm:p-8 text-white flex items-center gap-5" style="background: {{ $headBg }};">
            @if($user->photo_url)
                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                     class="w-20 h-20 rounded-full object-cover shrink-0" style="box-shadow: 0 0 0 4px rgba(255,255,255,.9);">
            @else
                <div class="w-20 h-20 rounded-full shrink-0 flex items-center justify-center text-2xl font-extrabold bg-white/15"
                     style="box-shadow: 0 0 0 4px rgba(255,255,255,.35);">
                    {{ $user->initials }}
                </div>
            @endif
            <div class="min-w-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $isAdmin ? 'bg-white/10 border border-white/20' : 'bg-orange-500' }}">
                    <i class="fa-solid {{ $isAdmin ? 'fa-user-shield' : 'fa-suitcase-rolling' }}"></i>
                    {{ $isAdmin ? 'Admin' : 'Customer' }}
                </span>
                <h1 class="text-2xl font-extrabold mt-2 truncate">{{ $user->name }}</h1>
                <p class="text-sm text-white/70 truncate">{{ $user->email }}</p>
                <p class="text-xs text-white/50 mt-1">Bergabung sejak {{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="mt-6 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                <i class="fa-solid fa-circle-check mr-1"></i> Profil berhasil diperbarui.
            </div>
        @endif

        {{-- Form profil --}}
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
              class="mt-6 bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 space-y-6"
              x-data="{ preview: '{{ $user->photo_url }}', remove: false }">
            @csrf
            @method('PATCH')
            <input type="hidden" name="remove_photo" :value="remove ? 1 : 0">

            <h2 class="font-bold text-slate-900 text-lg">Data Diri</h2>

            {{-- Foto --}}
            <div class="flex items-center gap-5">
                <div class="shrink-0">
                    <img x-show="preview" :src="preview" alt="Foto profil"
                         class="w-24 h-24 rounded-full object-cover"
                         style="box-shadow: 0 0 0 4px #fff, 0 0 0 6px {{ $accent }};">
                    <div x-show="!preview"
                         class="w-24 h-24 rounded-full flex items-center justify-center text-3xl font-extrabold text-white"
                         style="background: {{ $accent }}; box-shadow: 0 0 0 4px #fff, 0 0 0 6px {{ $accent }};">
                        {{ $user->initials }}
                    </div>
                </div>

                <div>
                    <input type="file" name="photo" x-ref="file" class="hidden"
                           accept="image/png,image/jpeg,image/webp"
                           @change="const f = $refs.file.files[0]; if (f) { preview = URL.createObjectURL(f); remove = false; }">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="$refs.file.click()"
                                class="px-4 py-2 rounded-xl text-sm font-bold text-white" style="background: {{ $accent }};">
                            <i class="fa-solid fa-camera mr-1"></i> Pilih Foto
                        </button>
                        <button type="button" x-show="preview" @click="preview = ''; remove = true; $refs.file.value = ''"
                                class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 border border-slate-200 hover:border-red-300 hover:text-red-500">
                            Hapus
                        </button>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
                    @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Nama --}}
            <div>
                <label for="name" class="text-sm font-semibold text-slate-700">Nama</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="text-sm font-semibold text-slate-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- No HP --}}
            <div>
                <label for="phone" class="text-sm font-semibold text-slate-700">Nomor HP</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                       placeholder="081234567890"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-[11px] text-slate-400 mt-1">
                    {{ $isAdmin ? 'Dipakai untuk kontak internal pengelola.' : 'Dipakai guide untuk menghubungi kamu soal tour.' }}
                </p>
            </div>

            <button type="submit"
                    class="px-6 py-3 rounded-xl text-white font-bold shadow-lg transition-all active:scale-95"
                    style="background: {{ $accent }};">
                Simpan Perubahan
            </button>
        </form>

        {{-- Ganti password --}}
        @if(session('status') === 'password-updated')
            <div class="mt-6 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                <i class="fa-solid fa-circle-check mr-1"></i> Password berhasil diganti.
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}"
              class="mt-6 bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 space-y-4">
            @csrf
            @method('PUT')

            <h2 class="font-bold text-slate-900 text-lg">Ganti Password</h2>

            <div>
                <label for="current_password" class="text-sm font-semibold text-slate-700">Password sekarang</label>
                <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                @if($errors->updatePassword->has('current_password'))
                    <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label for="new_password" class="text-sm font-semibold text-slate-700">Password baru</label>
                <input id="new_password" type="password" name="password" autocomplete="new-password"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                @if($errors->updatePassword->has('password'))
                    <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <div>
                <label for="password_confirmation" class="text-sm font-semibold text-slate-700">Ulangi password baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
            </div>

            <button type="submit" class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-lg transition-all active:scale-95">
                Ganti Password
            </button>
        </form>

    </div>
</section>
@endsection