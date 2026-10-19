<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Go Go Guide - Your Personal Tour Experts')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: { blue: '#1e5288', orange: '#f26522', darkBlue: '#133961' }
                },
                fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
            }
        }
    }
</script>
<style>
    .hero-gradient { background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 50%, #fff7f2 100%); }
    .guide-avatar-ring { box-shadow: 0 0 0 4px #ffffff, 0 0 0 6px #f26522; }
    [x-cloak] { display: none !important; }
</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-orange selection:text-white">

  {{-- NAVBAR --}}
<style>
    .gg-header { position: sticky; top: 0; z-index: 40; background: rgba(255,255,255,.95); backdrop-filter: blur(8px); border-bottom: 1px solid #f1f5f9; }
    .gg-bar { max-width: 1280px; margin: 0 auto; padding: 8px 16px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 4px 24px; }
    .gg-logo { display: flex; align-items: center; order: 1; }
    .gg-nav { order: 3; width: 100%; display: flex; justify-content: center; gap: 24px; padding-bottom: 4px; }
    .gg-nav a { color: #475569; font-weight: 600; font-size: 14px; text-decoration: none; }
    .gg-nav a:hover { color: #1e5288; }
    .gg-actions { order: 2; display: flex; align-items: center; gap: 12px; margin-left: auto; font-size: 14px; font-weight: 600; }
    .gg-actions a.gg-link { color: #475569; text-decoration: none; }
    .gg-actions a.gg-link:hover { color: #1e5288; }
    .gg-btn-outline { padding: 8px 18px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-weight: 600; font-size: 14px; cursor: pointer; }
    .gg-btn-outline:hover { border-color: #f26522; color: #f26522; }
    .gg-btn-orange { padding: 8px 18px; border-radius: 12px; background: #f26522; color: #fff !important; font-weight: 700; text-decoration: none; box-shadow: 0 4px 10px rgba(242,101,34,.25); }
    .gg-btn-orange:hover { background: #ea580c; }

    @media (min-width: 768px) {
        .gg-bar { flex-wrap: nowrap; height: 80px; padding-top: 0; padding-bottom: 0; }
        .gg-nav { order: 2; width: auto; padding-bottom: 0; gap: 32px; }
        .gg-actions { order: 3; margin-left: 0; }
    }
</style>

<header class="gg-header">
    <div class="gg-bar">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="gg-logo">
    @if(file_exists(public_path('images/logo.png')))
        <img src="{{ asset('images/logo.png') }}" alt="Go Go Guide"
             style="height:48px; width:auto; max-width:200px; object-fit:contain;">
    @else
        <span style="font-weight:800; font-size:24px; color:#1e5288;">GO <span style="color:#f26522;">GO</span> GUIDE</span>
    @endif
</a>

        {{-- Menu --}}
        <nav class="gg-nav">
            <a href="{{ route('home') }}#destinations">Destinations</a>
            <a href="{{ route('home') }}#guides">Local Guides</a>
            <a href="{{ route('home') }}#features">Why Us</a>
        </nav>

        {{-- Login / Booking Saya --}}
        <div class="gg-actions">
            @auth
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="gg-link">Dashboard</a>
    @if(auth()->user()->role === 'customer')
        <a href="{{ route('bookings.index') }}" class="gg-link">Booking Saya</a>
    @endif
    <a href="{{ route('profile.edit') }}" class="gg-link" style="display:flex; align-items:center; gap:8px;">
    @if(auth()->user()->photo_url)
        <img src="{{ auth()->user()->photo_url }}" alt=""
             style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
    @else
        <span style="width:32px; height:32px; border-radius:50%; background:#1e5288; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800;">
            {{ auth()->user()->initials }}
        </span>
    @endif
    Profil
</a>
    
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="gg-btn-outline">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="gg-link">Login</a>
                <a href="{{ route('register') }}" class="gg-btn-orange">Daftar</a>
            @endauth
        </div>

    </div>
</header>

    {{-- ISI HALAMAN --}}
    @yield('content')

    {{-- FOOTER --}}
    <footer class="bg-brand-darkBlue text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-slate-700">
                <div>
                    <div class="flex items-center gap-2 font-extrabold text-xl text-white mb-3">
                        <span>GO</span>
                        <span class="text-brand-orange">GO GUIDE</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Menghubungkan traveler dengan guide lokal bersertifikat untuk perjalanan yang bermakna.
                    </p>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-white mb-3">Quick Links</h5>
                    <ul class="space-y-2 text-xs text-slate-300">
                        <li><a href="{{ route('home') }}#destinations" class="hover:text-brand-orange">Popular Destinations</a></li>
                        <li><a href="{{ route('home') }}#guides" class="hover:text-brand-orange">Meet Guides</a></li>
                        <li><a href="{{ route('home') }}#features" class="hover:text-brand-orange">Why Go Go Guide</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-white mb-3">Become a Guide</h5>
                    <ul class="space-y-2 text-xs text-slate-300">
                        <li><a href="#" class="hover:text-brand-orange">Apply as Tour Guide</a></li>
                        <li><a href="#" class="hover:text-brand-orange">Guide Guidelines</a></li>
                        <li><a href="#" class="hover:text-brand-orange">Community Standards</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-white mb-3">Support</h5>
                    <p class="text-xs text-slate-400 mb-2">Butuh bantuan dengan booking?</p>
                    <a href="mailto:support@gogoguide.com" class="text-xs text-brand-orange font-bold hover:underline">support@gogoguide.com</a>
                </div>
            </div>
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400">
                <p>&copy; {{ date('Y') }} Go Go Guide. All rights reserved.</p>
                <div class="flex gap-4 mt-3 sm:mt-0">
                    <a href="#" class="hover:text-white"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="hover:text-white"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="hover:text-white"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>