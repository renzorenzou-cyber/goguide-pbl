@extends('layouts.site')

@section('title', 'Dashboard Guide - Go Go Guide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="rounded-3xl p-6 sm:p-8 text-white"
             style="background: linear-gradient(135deg, #065f46 0%, #059669 100%);">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/15 border border-white/25">
                <i class="fa-solid fa-map-location-dot"></i> Tour Guide
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-3">Halo, {{ auth()->user()->name }}</h1>
            <p class="text-emerald-50 text-sm mt-2">Jadwal dan tour kamu ada di sini.</p>
            <a href="{{ route('profile.edit') }}"
               class="inline-block mt-4 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/30 font-bold text-sm transition-colors">
                <i class="fa-solid fa-user-pen mr-1"></i> Edit Profil
            </a>
        </div>

        @if(! $guide)
            <div class="mt-6 rounded-2xl bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm px-5 py-4">
                Profil guide untuk akun ini belum dibuat. Hubungi admin.
            </div>
        @else

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Tour saya</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['tours'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Trip mendatang</p>
                <p class="text-2xl font-extrabold text-brand-blue mt-1">{{ $stats['upcoming'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Menunggu bayar</p>
                <p class="text-2xl font-extrabold text-yellow-500 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Nilai booking (paid)</p>
                <p class="text-xl font-extrabold text-green-600 mt-1">Rp {{ number_format($stats['value'], 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Trip mendatang --}}
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Trip Mendatang</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Tanggal</th>
                            <th class="text-left px-5 py-3">Tour</th>
                            <th class="text-left px-5 py-3">Customer</th>
                            <th class="text-left px-5 py-3">No. HP</th>
                            <th class="text-center px-5 py-3">Orang</th>
                            <th class="text-left px-5 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($upcoming as $b)
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap font-semibold">{{ $b->tour_date->format('d M Y') }}</td>
                                <td class="px-5 py-3">{{ $b->tour->title }}</td>
                                <td class="px-5 py-3">{{ $b->user->name }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $b->user->phone ?: '-' }}</td>
                                <td class="px-5 py-3 text-center">{{ $b->participants }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $b->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada trip yang sudah dibayar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tour saya --}}
        <div class="mt-8">
            <h2 class="font-bold text-slate-900 mb-3">Tour Saya</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($tours as $tour)
                    <a href="{{ route('tours.show', $tour->slug) }}"
                       class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition">
                        <p class="text-xs font-bold text-brand-orange uppercase">{{ $tour->region->name }}</p>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $tour->title }}</p>
                        <p class="text-sm text-slate-500 mt-1">
                            Rp {{ number_format($tour->price, 0, ',', '.') }} &middot; {{ $tour->duration }}
                        </p>
                    </a>
                @empty
                    <p class="text-slate-500 text-sm">Belum ada tour yang ditugaskan ke kamu.</p>
                @endforelse
            </div>
        </div>

        @endif

        <p class="mt-8 text-center text-sm">
            <a href="{{ route('home') }}" class="text-slate-500 hover:text-brand-blue">&larr; Lihat website</a>
        </p>
    </div>
</section>
@endsection