@extends('layouts.site')

@section('title', $guide->nama_guide . ' - Guide di GoGuide')

@section('content')
@php
    $inisial = collect(explode(' ', $guide->nama_guide))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
@endphp

<div class="max-w-5xl mx-auto px-4 py-10">

    <a href="{{ route('guides.index') }}" class="text-sm text-brand-blue hover:underline">&larr; Semua guide</a>

    {{-- HEADER PROFIL --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 mt-4">
        <div class="flex flex-col sm:flex-row gap-6 sm:items-center">
            @if($guide->foto_url)
                <img src="{{ $guide->foto_url }}" alt="{{ $guide->nama_guide }}" class="w-32 h-32 rounded-full object-cover guide-avatar-ring shrink-0">
            @else
                <div class="w-32 h-32 rounded-full guide-avatar-ring shrink-0 bg-brand-blue text-white flex items-center justify-center text-4xl font-extrabold">{{ $inisial }}</div>
            @endif

            <div class="min-w-0">
                <h1 class="text-3xl font-bold">{{ $guide->nama_guide }}</h1>
                <p class="mt-2 text-sm font-bold text-slate-700">
                    <i class="fa-solid fa-star text-amber-400"></i> {{ number_format($guide->rating, 1) }}
                    <span class="ml-2 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">
                        <i class="fa-solid fa-circle-check"></i> Terverifikasi
                    </span>
                </p>
                @if($guide->bio_singkat)
                    <p class="text-slate-600 mt-3">{{ $guide->bio_singkat }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- TENTANG SAYA --}}
    @if($guide->tentang_saya)
        <div class="bg-white rounded-2xl border border-slate-100 p-6 mt-6">
            <h2 class="font-bold text-slate-900 text-lg">Tentang Saya</h2>
            <div class="text-sm text-slate-600 mt-3 leading-relaxed">
                {!! nl2br(e($guide->tentang_saya)) !!}
            </div>
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6 mt-6">
        {{-- PENGALAMAN --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6">
            <h2 class="font-bold text-slate-900 text-lg">Pengalaman</h2>
            <div class="text-sm text-slate-600 mt-3 leading-relaxed">
                @if($guide->pengalaman)
                    {!! nl2br(e($guide->pengalaman)) !!}
                @else
                    <span class="text-slate-400">Belum diisi.</span>
                @endif
            </div>
        </div>

        {{-- BAHASA --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6">
            <h2 class="font-bold text-slate-900 text-lg">Bahasa</h2>
            <div class="mt-3 space-y-2">
                @forelse($guide->bahasa as $b)
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700">{{ $b->nama_bahasa }}</span>
                        <span class="px-2.5 py-1 rounded-full bg-blue-100 text-brand-blue text-xs font-semibold">{{ $b->pivot->tingkat_kemampuan }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum diisi.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- PAKET YANG DILAYANI --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 mt-6">
        <h2 class="font-bold text-slate-900 text-lg">Paket yang Dilayani</h2>
        <div class="grid sm:grid-cols-2 gap-4 mt-4">
            @forelse($guide->paket as $p)
                <a href="{{ route('paket.show', $p) }}" class="block p-4 rounded-xl border border-slate-200 hover:border-brand-orange hover:shadow transition">
                    <p class="font-bold text-slate-900">{{ $p->nama_paket }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $p->durasi }}</p>
                    <p class="text-brand-blue font-extrabold mt-2">
                        Rp{{ number_format($p->harga_paket, 0, ',', '.') }}
                        <span class="text-xs font-semibold text-slate-400">/ peserta</span>
                    </p>
                    <p class="text-xs font-bold text-brand-orange mt-2">Lihat paket dan pesan <i class="fa-solid fa-arrow-right"></i></p>
                </a>
            @empty
                <p class="text-sm text-slate-400">Belum ada paket.</p>
            @endforelse
        </div>
    </div>

    {{-- KETERSEDIAAN --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 mt-6">
        <h2 class="font-bold text-slate-900 text-lg">Tanggal Tersedia</h2>
        <p class="text-xs text-slate-500 mt-1">Maksimal 30 tanggal terdekat yang belum dipesan.</p>
        <div class="flex flex-wrap gap-2 mt-4">
            @forelse($tanggalTersedia as $t)
                <span class="px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                    {{ \Carbon\Carbon::parse($t)->locale('id')->translatedFormat('D, d M Y') }}
                </span>
            @empty
                <p class="text-sm text-slate-400">Belum ada tanggal tersedia.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection