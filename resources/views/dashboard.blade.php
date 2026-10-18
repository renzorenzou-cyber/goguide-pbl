@extends('layouts.site')

@section('title', 'Dashboard - Go Go Guide')

@section('content')
@php
    $badges = [
        'pending'   => 'bg-yellow-100 text-yellow-700',
        'paid'      => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
@endphp

<section class="py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <div class="rounded-3xl p-6 sm:p-8 text-white"
             style="background: linear-gradient(135deg, #133961 0%, #1e5288 100%);">
            <p class="text-blue-200 text-sm">Selamat datang kembali</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="text-blue-100 text-sm mt-2">Mau jelajah ke mana hari ini?</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('home') }}#destinations"
                   class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 font-bold text-sm shadow-lg transition-colors">
                    <i class="fa-solid fa-compass mr-1"></i> Cari Tour
                </a>
                <a href="{{ route('bookings.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/30 font-bold text-sm transition-colors">
                    Semua Booking Saya
                </a>
                <a href="{{ route('profile.edit') }}"
   class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/30 font-bold text-sm transition-colors">
    <i class="fa-solid fa-user-pen mr-1"></i> Edit Profil
</a>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 text-center">
                <p class="text-3xl font-extrabold text-brand-blue">{{ $stats['total'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Total booking</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 text-center">
                <p class="text-3xl font-extrabold text-yellow-500">{{ $stats['pending'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Menunggu bayar</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 text-center">
                <p class="text-3xl font-extrabold text-green-600">{{ $stats['paid'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Sudah dibayar</p>
            </div>
        </div>

        @if($upcoming)
            <a href="{{ route('bookings.show', $upcoming) }}"
               class="block mt-6 bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition">
                <p class="text-xs font-bold text-brand-orange uppercase tracking-wider">Trip terdekat</p>
                <h2 class="text-lg font-bold text-slate-900 mt-1">{{ $upcoming->tour->title }}</h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $upcoming->tour->region->name }} &middot;
                    {{ $upcoming->tour_date->format('d M Y') }} &middot;
                    {{ $upcoming->participants }} orang
                </p>
            </a>
        @endif

        <div class="mt-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-slate-900">Booking Terbaru</h2>
                <a href="{{ route('bookings.index') }}" class="text-sm text-brand-blue font-semibold hover:underline">Lihat semua</a>
            </div>

            <div class="space-y-3">
                @forelse($recent as $booking)
                    <a href="{{ route('bookings.show', $booking) }}"
                       class="flex items-center justify-between gap-3 bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition">
                        <div>
                            <p class="text-xs text-slate-400">{{ $booking->booking_code }}</p>
                            <p class="font-bold text-slate-900">{{ $booking->tour->title }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $booking->tour->region->name }} &middot; {{ $booking->tour_date->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded text-xs font-semibold uppercase {{ $badges[$booking->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $booking->status }}
                            </span>
                            <p class="text-sm font-bold mt-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>
                    </a>
                @empty
                    <div class="bg-white rounded-2xl p-8 text-center text-slate-500 border border-slate-100">
                        Kamu belum punya booking.
                        <a href="{{ route('home') }}#destinations" class="text-brand-blue font-semibold underline">Cari tour</a>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</section>
@endsection