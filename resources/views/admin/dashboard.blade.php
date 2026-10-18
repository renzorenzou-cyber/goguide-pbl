@extends('layouts.site')

@section('title', 'Dashboard Admin - Go Go Guide')

@section('content')
@php
    $badges = [
        'pending'   => 'bg-yellow-100 text-yellow-700',
        'paid'      => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
@endphp

<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="rounded-3xl p-6 sm:p-8 text-white bg-slate-900">
            <p class="text-slate-400 text-sm">Panel Admin</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">Halo, {{ auth()->user()->name }}</h1>
            <p class="text-slate-300 text-sm mt-2">Ringkasan aktivitas Go Go Guide.</p>
            <a href="{{ route('profile.edit') }}"
   class="inline-block mt-4 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-sm transition-colors">
    <i class="fa-solid fa-user-pen mr-1"></i> Edit Profil
</a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Pendapatan (paid)</p>
                <p class="text-2xl font-extrabold text-green-600 mt-1">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Total booking</p>
                <p class="text-2xl font-extrabold text-brand-blue mt-1">{{ $stats['bookings'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Menunggu bayar</p>
                <p class="text-2xl font-extrabold text-yellow-500 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Sudah dibayar</p>
                <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $stats['paid'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Customer</p>
                <p class="text-2xl font-extrabold text-brand-blue mt-1">{{ $stats['customers'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Tour aktif</p>
                <p class="text-2xl font-extrabold text-brand-orange mt-1">{{ $stats['tours'] }}</p>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">10 Booking Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Kode</th>
                            <th class="text-left px-5 py-3">Customer</th>
                            <th class="text-left px-5 py-3">Tour</th>
                            <th class="text-left px-5 py-3">Tanggal</th>
                            <th class="text-right px-5 py-3">Total</th>
                            <th class="text-center px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recent as $b)
                            <tr>
                                <td class="px-5 py-3 font-mono text-xs">{{ $b->booking_code }}</td>
                                <td class="px-5 py-3">{{ $b->user->name }}</td>
                                <td class="px-5 py-3">{{ $b->tour->title }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $b->tour_date->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2.5 py-1 rounded text-xs font-semibold uppercase {{ $badges[$b->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $b->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada booking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-6 text-center text-sm">
            <a href="{{ route('home') }}" class="text-slate-500 hover:text-brand-blue">&larr; Lihat website</a>
        </p>
    </div>
</section>
@endsection