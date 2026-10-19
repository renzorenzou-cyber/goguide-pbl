@extends('layouts.site')

@section('title', 'Masuk / Daftar - Go Go Guide')

@section('content')
@php
    $mode    = old('form', $mode ?? 'login');
    $portal  = in_array(old('portal'), ['admin', 'guide'], true) ? old('portal') : 'customer';
    $loginErr = old('form') === 'login';
    $regErr   = old('form') === 'register';

    // Foto background: pakai punya lu kalau ada, kalau nggak pakai cadangan
    $pickBg = function (string $name, string $fallback) {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (file_exists(public_path("images/auth/$name.$ext"))) {
                return asset("images/auth/$name.$ext");
            }
        }
        return asset($fallback);
    };
    $bgLogin    = $pickBg('login-bg', 'images/login-bg.jpg');
    $bgRegister = $pickBg('register1-bg', 'images/register1-bg.jpg');
@endphp

<style>
    @keyframes gaFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    @keyframes gaBlob  { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(30px,-20px) scale(1.1); } }
    @keyframes gaRise  { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    .ga-float { animation: gaFloat 3.5s ease-in-out infinite; }
    .ga-blob  { animation: gaBlob 9s ease-in-out infinite; }
    .ga-rise  { animation: gaRise .7s cubic-bezier(.2,.8,.2,1) both; }

    .ga-wrap { position: relative; width: 100%; max-width: 900px; min-height: 700px; margin: 0 auto;
               background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.3); border-radius: 28px; overflow: hidden;
               box-shadow: 0 25px 60px -12px rgba(19,57,97,.28); }

        /* Form (kiri = login, kanan = daftar) */
    .ga-form { position: absolute; top: 0; left: 0; width: 50%; height: 100%;
               display: flex; justify-content: center; background: rgba(255,255,255,.94); 
              /* backdrop-filter: blur(12px); */
               padding: 24px 28px; overflow-y: auto;
               visibility: hidden; opacity: 0;
               transition: transform .65s ease-in-out, opacity .65s ease-in-out, visibility 0s linear .65s; }

    /* Login = aktif secara default */
    .ga-login { z-index: 2; visibility: visible; opacity: 1; transition-delay: 0s, 0s, 0s; }

    /* Mode daftar: login geser keluar lalu disembunyikan, daftar geser masuk */
    .ga-wrap.is-register .ga-login    { transform: translateX(100%); opacity: 0; visibility: hidden;
                                        transition-delay: 0s, 0s, .65s; }
    .ga-wrap.is-register .ga-register { transform: translateX(100%); opacity: 1; visibility: visible;
                                        z-index: 5; transition-delay: 0s, 0s, 0s; }

    .ga-inner { width: 100%; max-width: 340px; margin: auto; }

    /* Panel overlay yang bergeser */
    .ga-overlay-box { position: absolute; top: 0; left: 50%; width: 50%; height: 100%;
                      overflow: hidden; z-index: 100; transition: transform .65s ease-in-out; }
    .ga-wrap.is-register .ga-overlay-box { transform: translateX(-100%); }
    .ga-overlay { position: relative; left: -100%; width: 200%; height: 100%; color: #fff;
                  background: linear-gradient(135deg, #133961 0%, #1e5288 55%, #f26522 160%);
                  transition: transform .65s ease-in-out; }
    .ga-wrap.is-register .ga-overlay { transform: translateX(50%); }
    .ga-panel { position: absolute; top: 0; width: 50%; height: 100%;
                display: flex; flex-direction: column; align-items: center; justify-content: center;
                text-align: center; padding: 0 40px; transition: transform .65s ease-in-out; }
    .ga-panel-left  { transform: translateX(-20%); }
    .ga-wrap.is-register .ga-panel-left  { transform: translateX(0); }
    .ga-panel-right { right: 0; }
    .ga-wrap.is-register .ga-panel-right { transform: translateX(20%); }

    .ga-ghost { margin-top: 20px; padding: 11px 32px; border-radius: 999px; border: 2px solid #fff;
                background: transparent; color: #fff; font-weight: 700; font-size: 14px; cursor: pointer;
                transition: all .25s; }
    .ga-ghost:hover { background: #fff; color: #1e5288; transform: scale(1.05); }

            /* Foto miring + shader di panel yang bergeser */
    .ga-photo { position: absolute; inset: 0; overflow: hidden; z-index: 0; }
    .ga-photo-right { clip-path: polygon(16% 0, 100% 0, 100% 100%, 0 100%); }
    .ga-photo-left  { clip-path: polygon(0 0, 84% 0, 100% 100%, 0 100%); }
    .ga-photo-img { position: absolute; inset: 0; background-size: cover; background-position: center; }
    .ga-photo-shade { position: absolute; inset: 0; }
    .ga-photo-right .ga-photo-shade {
        background: linear-gradient(135deg, rgba(19,57,97,.90) 0%, rgba(30,82,136,.68) 55%, rgba(242,101,34,.50) 130%); }
    .ga-photo-left .ga-photo-shade {
        background: linear-gradient(225deg, rgba(19,57,97,.90) 0%, rgba(30,82,136,.68) 55%, rgba(242,101,34,.50) 130%); }
    .ga-panel > *:not(.ga-photo) { position: relative; z-index: 2; }
    .ga-panel-right { padding-left: 56px; }
    .ga-panel-left  { padding-right: 56px; }
    @keyframes gaZoom { from { transform: scale(1); } to { transform: scale(1.12); } }

        /* ===== Background halaman: foto miring + shader ===== */
    @keyframes gaZoom { from { transform: scale(1); } to { transform: scale(1.12); } }

    .ga-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .ga-bg-layer { position: absolute; inset: 0; transition: opacity .8s ease; }

    /* Login: dasar biru. Daftar: dasar oranye (berlawanan) */
    .ga-bg-login    { background: linear-gradient(135deg, #0b2545 0%, #133961 55%, #1e5288 100%); opacity: 1; }
    .ga-bg-register { background: linear-gradient(225deg, #7c2d12 0%, #c2410c 55%, #f26522 100%); opacity: 0; }
    .ga-section.is-register .ga-bg-login    { opacity: 0; }
    .ga-section.is-register .ga-bg-register { opacity: 1; }

    /* Foto dipotong miring, arahnya berlawanan */
    .ga-bg-tilt { position: absolute; inset: 0; }
    .ga-bg-login    .ga-bg-tilt { clip-path: polygon(34% 0, 100% 0, 100% 100%, 14% 100%); }
    .ga-bg-register .ga-bg-tilt { clip-path: polygon(0 0, 66% 0, 86% 100%, 0 100%); }

    .ga-bg-photo { position: absolute; inset: 0; background-size: cover; background-position: center;}
    .ga-bg-shade { position: absolute; inset: 0; }

    /* Shader: warna berlawanan */
    .ga-bg-login .ga-bg-shade {
        background: linear-gradient(135deg, rgba(19,57,97,.80) 0%, rgba(30,82,136,.55) 60%, rgba(242,101,34,.45) 130%); }
    .ga-bg-register .ga-bg-shade {
        background: linear-gradient(225deg, rgba(194,65,12,.80) 0%, rgba(242,101,34,.55) 60%, rgba(19,57,97,.50) 130%); }

            /* Biar animasi geser lebih halus (dikerjakan GPU) */
    .ga-form, .ga-overlay-box, .ga-overlay, .ga-panel { will-change: transform; backface-visibility: hidden; }
    .ga-bg-layer { will-change: opacity; }

    /* Hormati pengguna yang matikan animasi di perangkatnya */
    @media (prefers-reduced-motion: reduce) {
        .ga-form, .ga-overlay-box, .ga-overlay, .ga-panel, .ga-bg-layer { transition-duration: .01s !important; }
        .ga-float, .ga-blob { animation: none !important; }
    }

    /* Tab khusus HP */
    .ga-tabs { display: none; }

    @media (max-width: 767px) {
        .ga-wrap { min-height: auto; border-radius: 24px; }
        .ga-overlay-box { display: none; }
        .ga-form { position: static; width: 100%; height: auto; display: none;
                   transform: none !important; opacity: 1 !important; visibility: visible !important;
                   padding: 28px 20px; }
        .ga-wrap:not(.is-register) .ga-login    { display: flex; }
        .ga-wrap.is-register .ga-register       { display: flex; }
        .ga-tabs { display: flex; gap: 6px; padding: 8px; background: #f1f5f9; }
        .ga-tabs button { flex: 1; padding: 10px; border-radius: 12px; font-weight: 700; font-size: 14px; color: #64748b; }
        .ga-tabs button.on { background: #fff; color: #1e5288; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    }
</style>

<section class="ga-section relative overflow-hidden py-10 sm:py-14"
         :class="{ 'is-register': mode === 'register' }"
         x-data="{
            mode: '{{ $mode }}',
            portal: '{{ $portal }}',
            showLogin: false,
            showReg: false,
            setMode(m) {
                this.mode = m;
                history.replaceState(null, '', m === 'register' ? '{{ route('register') }}' : '{{ route('login') }}');
            }
         }">

    {{-- BACKGROUND: foto miring + shader --}}
    <div class="ga-bg">
        <div class="ga-bg-layer ga-bg-login">
            <div class="ga-bg-tilt">
                <div class="ga-bg-photo" style="background-image:url('{{ $bgLogin }}');"></div>
                <div class="ga-bg-shade"></div>
            </div>
        </div>
        <div class="ga-bg-layer ga-bg-register">
            <div class="ga-bg-tilt">
                <div class="ga-bg-photo" style="background-image:url('{{ $bgRegister }}');"></div>
                <div class="ga-bg-shade"></div>
            </div>
        </div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 ga-rise">

        <div class="ga-wrap" :class="{ 'is-register': mode === 'register' }">

            {{-- Tab (cuma muncul di HP) --}}
            <div class="ga-tabs">
                <button type="button" @click="setMode('login')" :class="{ 'on': mode === 'login' }">Login</button>
                <button type="button" @click="setMode('register')" :class="{ 'on': mode === 'register' }">Daftar</button>
            </div>

            {{-- ============ FORM LOGIN ============ --}}
            <div class="ga-form ga-login" :inert="mode !== 'login'">
                <form method="POST" action="{{ route('login') }}" class="ga-inner space-y-4">
                    @csrf
                    <input type="hidden" name="form" value="login">
                    <input type="hidden" name="portal" :value="portal">

                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-900">Masuk</h1>
                        <p class="text-sm text-slate-500 mt-1">Selamat datang kembali!</p>
                    </div>

                    @if (session('status'))
                        <p class="text-sm text-green-600">{{ session('status') }}</p>
                    @endif

                    {{-- Switch Customer / Guide / Admin --}}
<div class="grid grid-cols-3 gap-1 p-1 rounded-xl bg-slate-100 text-xs sm:text-sm font-bold">
    <button type="button" @click="portal = 'customer'"
            class="py-2 rounded-lg transition-all"
            :class="portal === 'customer' ? 'bg-white text-brand-orange shadow' : 'text-slate-500'">
        <i class="fa-solid fa-suitcase-rolling mr-1"></i> Customer
    </button>
    <button type="button" @click="portal = 'guide'"
            class="py-2 rounded-lg transition-all"
            :class="portal === 'guide' ? 'bg-emerald-600 text-white shadow' : 'text-slate-500'">
        <i class="fa-solid fa-map-location-dot mr-1"></i> Guide
    </button>
    <button type="button" @click="portal = 'admin'"
            class="py-2 rounded-lg transition-all"
            :class="portal === 'admin' ? 'bg-slate-900 text-white shadow' : 'text-slate-500'">
        <i class="fa-solid fa-user-shield mr-1"></i> Admin
    </button>
</div>

                    <div>
                        <label for="login-email" class="text-sm font-semibold text-slate-700">Email</label>
                        <input id="login-email" type="email" name="email" required autocomplete="username"
                               value="{{ $loginErr ? old('email') : '' }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                        @if($loginErr)
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label for="login-password" class="text-sm font-semibold text-slate-700">Password</label>
                        <div class="relative mt-1">
                            <input id="login-password" :type="showLogin ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-3 pr-12 text-sm focus:border-brand-blue focus:ring-brand-blue">
                            <button type="button" @click="showLogin = !showLogin"
                                    class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-slate-600">
                                <i :class="showLogin ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                            </button>
                        </div>
                        @if($loginErr)
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-slate-600">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-blue focus:ring-brand-blue">
                            Ingat saya
                        </label>
                        @if (Route::has('password.request'))
                            <a x-show="portal === 'customer'" href="{{ route('password.request') }}"
                               class="text-brand-blue hover:underline">Lupa password?</a>
                        @endif
                    </div>

                    <button type="submit"
        class="w-full py-3 rounded-xl text-white font-bold shadow-lg transition-all active:scale-95"
        :class="{
            'bg-slate-900 hover:bg-slate-800': portal === 'admin',
            'bg-emerald-600 hover:bg-emerald-700': portal === 'guide',
            'bg-brand-orange hover:bg-orange-600': portal === 'customer'
        }"
        x-text="{ admin: 'Masuk sebagai Admin', guide: 'Masuk sebagai Guide', customer: 'Masuk' }[portal]">
</button>

                    <p x-show="portal === 'admin'" x-cloak class="text-center text-xs text-slate-400">
    Akun admin dibuat oleh pengelola, tidak ada pendaftaran mandiri.
</p>
<p x-show="portal === 'guide'" x-cloak class="text-center text-xs text-slate-400">
    Akun guide dibuat oleh admin. Mau jadi guide? Hubungi support@gogoguide.com.
</p>
                </form>
            </div>

            {{-- ============ FORM DAFTAR ============ --}}
            <div class="ga-form ga-register" :inert="mode !== 'register'">
                <form method="POST" action="{{ route('register') }}" class="ga-inner space-y-4">
                    @csrf
                    <input type="hidden" name="form" value="register">

                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-900">Buat Akun</h1>
                        <p class="text-sm text-slate-500 mt-1">Gratis, langsung bisa booking tour.</p>
                    </div>

                    <div>
                        <label for="reg-name" class="text-sm font-semibold text-slate-700">Nama</label>
                        <input id="reg-name" type="text" name="name" required autocomplete="name"
                               value="{{ $regErr ? old('name') : '' }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                        @if($regErr)
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label for="reg-email" class="text-sm font-semibold text-slate-700">Email</label>
                        <input id="reg-email" type="email" name="email" required autocomplete="username"
                               value="{{ $regErr ? old('email') : '' }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                        @if($regErr)
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label for="reg-password" class="text-sm font-semibold text-slate-700">Password</label>
                        <div class="relative mt-1">
                            <input id="reg-password" :type="showReg ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-3 pr-12 text-sm focus:border-brand-blue focus:ring-brand-blue">
                            <button type="button" @click="showReg = !showReg"
                                    class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-slate-600">
                                <i :class="showReg ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                            </button>
                        </div>
                        @if($regErr)
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                        <p class="text-[11px] text-slate-400 mt-1">Minimal 8 karakter.</p>
                    </div>

                    <div>
                        <label for="reg-password2" class="text-sm font-semibold text-slate-700">Ulangi Password</label>
                        <input id="reg-password2" :type="showReg ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                               class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-blue focus:ring-brand-blue">
                    </div>

                    <button type="submit"
                            class="w-full py-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold shadow-lg transition-all active:scale-95">
                        Daftar Sekarang
                    </button>
                </form>
            </div>

            {{-- ============ PANEL YANG BERGESER ============ --}}
<div class="ga-overlay-box">
    <div class="ga-overlay">

        {{-- Panel kiri (kelihatan saat mode Daftar) --}}
        <div class="ga-panel ga-panel-left">
            <div class="ga-photo ga-photo-left">
                <div class="ga-photo-img" style="background-image:url('{{ asset('images/tours/uluwatu.jpg') }}');"></div>
                <div class="ga-photo-shade"></div>
            </div>
            <div class="ga-float w-16 h-16 rounded-2xl bg-white/15 flex items-center justify-center text-3xl mb-5">
                <i class="fa-solid fa-right-to-bracket"></i>
            </div>
            <h2 class="text-2xl font-extrabold">Sudah punya akun?</h2>
            <p class="text-sm text-blue-100 mt-2">Masuk dan lanjutkan booking tour favoritmu bareng guide lokal.</p>
            <button type="button" class="ga-ghost" @click="setMode('login')">Login</button>
        </div>

        {{-- Panel kanan (kelihatan saat mode Login) --}}
        <div class="ga-panel ga-panel-right">
            <div class="ga-photo ga-photo-right">
                <div class="ga-photo-img" style="background-image:url('{{ asset('images/hero.jpg') }}');"></div>
                <div class="ga-photo-shade"></div>
            </div>
            <div class="ga-float w-16 h-16 rounded-2xl bg-white/15 flex items-center justify-center text-3xl mb-5">
                <i class="fa-solid fa-compass"></i>
            </div>
            <h2 class="text-2xl font-extrabold">Halo, Traveler!</h2>
            <p class="text-sm text-blue-100 mt-2">Daftar gratis dan mulai jelajahi Bali bareng guide lokal bersertifikat.</p>
            <button type="button" class="ga-ghost" @click="setMode('register')">Daftar</button>
        </div>

    </div>
</div>

        </div>

        <p class="text-center mt-6 text-sm">
            <a href="{{ route('home') }}" class="text-white/80 hover:text-white">&larr; Kembali ke beranda</a>
        </p>
    </div>
</section>
@endsection