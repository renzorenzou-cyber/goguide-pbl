@extends('layouts.site')

@section('title', 'Rekap Fee Guide - Go Guide')

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $tabs = ['belum' => 'Belum dibayar', 'sudah' => 'Sudah dibayar'];
@endphp
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard admin
        </a>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Rekap Fee Guide</h1>
            <a href="{{ route('admin.bbm.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">
                Catatan BBM
            </a>
        </div>
        <p class="text-sm text-slate-500 mt-1">Fee dihitung dari booking yang sudah selesai. Tandai sudah dibayar setelah fee ditransfer ke guide.</p>

        @include('admin.partials.flash')

        {{-- Ringkasan --}}
        <div class="grid sm:grid-cols-2 gap-4 mt-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Fee belum dibayar ({{ $ringkas['belum_n'] }} booking)</p>
                <p class="text-2xl font-extrabold text-yellow-500 mt-1">{{ $rp($ringkas['belum_total']) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Fee sudah dibayar ({{ $ringkas['sudah_n'] }} booking)</p>
                <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $rp($ringkas['sudah_total']) }}</p>
            </div>
        </div>

        {{-- Rekap per guide --}}
        <div class="mt-6 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Fee belum dibayar per guide</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Guide</th>
                            <th class="text-center px-5 py-3">Tour selesai</th>
                            <th class="text-right px-5 py-3">Total fee</th>
                            <th class="text-center px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($perGuide as $g)
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $g->guide?->nama_guide ?? 'Guide #' . $g->id_guide }}</td>
                                <td class="px-5 py-3 text-center">{{ $g->jumlah }}</td>
                                <td class="px-5 py-3 text-right font-semibold whitespace-nowrap">{{ $rp($g->total_fee) }}</td>
                                <td class="px-5 py-3 text-center">
                                    <a href="{{ route('admin.fee.index', ['status' => 'belum', 'guide' => $g->id_guide]) }}"
                                       class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-300 hover:border-brand-orange hover:text-brand-orange">Lihat rincian</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500">Tidak ada fee yang menunggu dibayar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Rincian per booking --}}
        <nav class="mt-8 flex flex-wrap gap-2" aria-label="Status fee">
            @foreach ($tabs as $kode => $label)
                <a href="{{ route('admin.fee.index', array_filter(['status' => $kode, 'guide' => $filterGuide?->id_guide])) }}" @if($tab === $kode) aria-current="page" @endif
                   class="px-4 py-2 rounded-xl text-sm font-bold border transition
                          {{ $tab === $kode ? 'bg-brand-blue text-white border-brand-blue' : 'bg-white text-slate-600 border-slate-200 hover:border-brand-orange hover:text-brand-orange' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <form method="GET" class="mt-4 flex flex-wrap items-center gap-2">
            <input type="hidden" name="status" value="{{ $tab }}">
            @if ($filterGuide) <input type="hidden" name="guide" value="{{ $filterGuide->id_guide }}"> @endif
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kode booking"
                   class="w-full max-w-sm rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">
            <button class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">Cari</button>
            @if ($filterGuide)
                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-orange-50 text-brand-orange text-xs font-bold">
                    Guide: {{ $filterGuide->nama_guide }}
                    <a href="{{ route('admin.fee.index', ['status' => $tab]) }}" class="underline">hapus filter</a>
                </span>
            @endif
        </form>

        <div class="mt-4 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Booking</th>
                            <th class="text-left px-5 py-3">Guide</th>
                            <th class="text-left px-5 py-3">Tanggal tour</th>
                            <th class="text-right px-5 py-3">Total bayar</th>
                            <th class="text-right px-5 py-3">Fee guide</th>
                            <th class="text-left px-5 py-3">{{ $tab === 'sudah' ? 'Dibayar pada' : 'Aksi' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($bookings as $b)
                            <tr class="align-top">
                                <td class="px-5 py-3">
                                    <p class="font-mono text-xs">{{ $b->kode_booking }}</p>
                                    <p class="font-semibold">{{ $b->paket?->nama_paket }}</p>
                                </td>
                                <td class="px-5 py-3">{{ $b->guide?->nama_guide }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $b->tanggal_booking->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $rp($b->total_bayar) }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap font-semibold text-emerald-700">{{ $rp($b->fee_guide) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if ($tab === 'sudah')
                                        {{ $b->tanggal_bayar_guide?->translatedFormat('d M Y H:i') ?? '-' }}
                                    @else
                                        <form method="POST" action="{{ route('admin.fee.pay', $b->id_booking) }}"
                                              onsubmit="return confirm('Tandai fee {{ $rp($b->fee_guide) }} sudah dibayar ke {{ addslashes($b->guide?->nama_guide) }}?')">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700">Tandai dibayar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">Tidak ada data pada tab ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $bookings->links('admin.partials.pagination') }}</div>
    </div>
</section>
@endsection