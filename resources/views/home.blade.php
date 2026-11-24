@extends('layouts.site')

@section('title', 'Go Guide - Paket Wisata Bali bersama Guide Lokal')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden py-16 lg:py-24" style="background: linear-gradient(135deg, #133961 0%, #1e5288 100%);">
    <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('{{ asset('images/cta.jpg') }}');"></div>
    <div class="relative z-10 max-w-3xl mx-auto px-4 text-center">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-100 text-brand-orange text-xs font-bold">
            <i class="fa-solid fa-star"></i> Guide Lokal Terpercaya di Bali
        </span>
        <h1 class="mt-5 text-4xl sm:text-5xl font-extrabold text-white leading-tight">
            Jelajahi Bali bersama <span class="text-orange-400">Guide Lokal</span>
        </h1>
        <p class="mt-4 text-slate-100 text-base sm:text-lg">
            Pilih paket wisata, pilih guide dan tanggalnya, lalu booking dan bayar lewat virtual account.
        </p>

        <form method="GET" action="{{ route('home') }}#paket" class="mt-8 max-w-xl mx-auto">
            <div class="bg-white p-2 rounded-2xl shadow-xl flex flex-col sm:flex-row gap-2">
                <div class="flex-1 flex items-center px-3 py-2 bg-slate-50 rounded-xl border border-slate-200/60">
                    <i class="fa-solid fa-location-dot text-brand-orange text-lg mr-3"></i>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari paket atau objek wisata (Gunung Batur, Ubud...)"
                           class="w-full bg-transparent border-none focus:ring-0 text-slate-800 text-sm focus:outline-none placeholder-slate-400 font-medium p-0">
                </div>
                <button type="submit" class="px-6 py-3 bg-brand-blue hover:bg-brand-darkBlue text-white font-bold rounded-xl text-sm">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Search
                </button>
            </div>
        </form>
    </div>
</section>

{{-- PAKET WISATA --}}
<section id="paket" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Paket Wisata</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Pilih Paket Perjalananmu</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('home') }}#paket"
                   class="px-4 py-2 rounded-xl text-xs font-bold {{ !request('area') ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                @foreach($areas as $area)
                    <a href="{{ route('home', ['area' => $area->id_area]) }}#paket"
                       class="px-4 py-2 rounded-xl text-xs font-bold {{ request('area') == $area->id_area ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $area->nama_area }}
                    </a>
                @endforeach
            </div>
        </div>

        @if(request('q'))
            <p class="mb-6 text-sm text-slate-500">
                Hasil untuk <span class="font-bold text-slate-800">"{{ request('q') }}"</span> ({{ $totalPaket }} paket) &middot;
                <a href="{{ route('home') }}#paket" class="text-brand-blue font-semibold hover:underline">Reset</a>
            </p>
        @endif

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($pakets as $p)
                @include('partials.paket-card', ['p' => $p])
            @empty
                <p class="sm:col-span-2 lg:col-span-3 text-center text-slate-500 py-10">Belum ada paket untuk pilihan ini.</p>
            @endforelse
        </div>

        @if($totalPaket > $pakets->count())
            <div class="mt-10 text-center">
                <a href="{{ route('paket.index', request()->only('q', 'area')) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-darkBlue text-white font-bold text-sm shadow">
                    Lihat semua paket ({{ $totalPaket }}) <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>
</section>

{{-- GUIDE --}}
<section id="guide" class="py-16 bg-slate-100/70 border-y border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Guide Kami</span>
            <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Guide Lokal Berpengalaman</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($guides as $g)
                @php
                    $inisial = collect(explode(' ', $g->nama_guide))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
                @endphp
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center">
                    <div class="w-20 h-20 rounded-full mx-auto bg-brand-blue text-white flex items-center justify-center text-2xl font-extrabold guide-avatar-ring">{{ $inisial }}</div>
                    <h4 class="font-bold text-slate-800 text-lg mt-4">{{ $g->nama_guide }}</h4>
                    <div class="text-xs font-bold text-slate-700 my-2">
                        <i class="fa-solid fa-star text-amber-400"></i> {{ number_format($g->rating, 1) }}
                    </div>
                    <p class="text-xs text-slate-500 line-clamp-3">{{ $g->bio_singkat }}</p>
                    <p class="text-xs text-brand-blue font-semibold mt-3">{{ $g->paket_count }} paket ditangani</p>
                </div>
            @empty
                <p class="sm:col-span-2 lg:col-span-4 text-center text-slate-500">Belum ada guide.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- CARA MEMESAN --}}
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Cara Memesan</span>
            <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Empat Langkah Mudah</h2>
        </div>
        <div class="grid md:grid-cols-4 gap-6">
            @foreach([
                ['fa-map-location-dot', 'Pilih paket', 'Lihat info paket dan objek wisata yang dikunjungi.'],
                ['fa-user-group', 'Pilih guide dan tanggal', 'Sistem mengecek jadwal guide pada tanggal pilihanmu.'],
                ['fa-credit-card', 'Booking dan bayar', 'Dapatkan kode virtual account dan bayar lewat payment gateway.'],
                ['fa-envelope-circle-check', 'Terima bukti bayar', 'Bukti bayar dan data guide dikirim ke emailmu.'],
            ] as $i => [$ikon, $judul, $isi])
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-brand-blue flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid {{ $ikon }}"></i>
                    </div>
                    <p class="text-xs font-bold text-brand-orange">LANGKAH {{ $i + 1 }}</p>
                    <h3 class="text-lg font-bold text-slate-900">{{ $judul }}</h3>
                    <p class="text-slate-600 text-sm mt-2">{{ $isi }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- DAFTAR SEBAGAI GUIDE --}}
@php $linkDaftar = Route::has('guide.daftar') ? route('guide.daftar') : null; @endphp
<section id="daftar-guide" class="py-16" style="background: linear-gradient(135deg, #133961 0%, #1e5288 100%);">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-extrabold text-white">Ingin Jadi Guide di Go Guide?</h2>
        <p class="text-blue-100 mt-3 text-sm sm:text-base">
            Daftarkan dirimu, admin akan memeriksa datamu, dan akun dikirim lewat email setelah disetujui.
        </p>
        @if($linkDaftar)
            <a href="{{ $linkDaftar }}" class="inline-block mt-6 px-6 py-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold">Daftar sebagai Guide</a>
        @else
            <span class="inline-block mt-6 px-6 py-3 rounded-xl bg-white/10 border border-white/30 text-white font-bold">Pendaftaran segera dibuka</span>
        @endif
    </div>
</section>

{{-- KONTAK ADMIN --}}
<section id="kontak" class="py-16 bg-white">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Kontak</span>
        <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Butuh Bantuan?</h2>
        <p class="text-slate-600 text-sm mt-3">Hubungi admin untuk pertanyaan seputar paket, booking, dan pembayaran.</p>
        <a href="mailto:support@goguide.com" class="inline-block mt-5 px-6 py-3 rounded-xl bg-brand-blue text-white font-bold text-sm">
            <i class="fa-solid fa-envelope mr-1"></i> support@goguide.com
        </a>
        {{-- Tambahkan nomor WhatsApp admin di sini bila sudah ada --}}
    </div>
</section>

@endsection