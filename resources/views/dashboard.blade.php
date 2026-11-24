@extends('layouts.site')

@section('title', 'Dashboard Wisatawan - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        {{-- Informasi profil --}}
        <div class="rounded-3xl p-6 sm:p-8 text-white" style="background: linear-gradient(135deg, #133961 0%, #1e5288 100%);">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                @if($user->photo_url)
                    <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover shrink-0" style="box-shadow: 0 0 0 4px rgba(255,255,255,.9);">
                @else
                    <div class="w-20 h-20 rounded-full shrink-0 flex items-center justify-center text-2xl font-extrabold bg-white/15" style="box-shadow: 0 0 0 4px rgba(255,255,255,.35);">{{ $user->initials }}</div>
                @endif
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-orange-500">
                        <i class="fa-solid fa-suitcase-rolling"></i> Wisatawan
                    </span>
                    <h1 class="text-2xl font-extrabold mt-2 truncate">Halo, {{ $user->name }}</h1>
                    <p class="text-sm text-blue-100 truncate">{{ $user->email }}@if($user->phone) &middot; {{ $user->phone }}@endif</p>
                </div>
            </div>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('home') }}#paket" class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 font-bold text-sm shadow-lg">
                    <i class="fa-solid fa-compass mr-1"></i> Cari Paket Wisata
                </a>
                <a href="{{ route('profile.edit') }}" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/30 font-bold text-sm">
                    <i class="fa-solid fa-user-pen mr-1"></i> Edit Profil
                </a>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 text-center">
                <p class="text-3xl font-extrabold text-brand-blue">{{ $ringkasan['total'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Total pemesanan</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 text-center">
                <p class="text-3xl font-extrabold text-yellow-500">{{ $ringkasan['belum_bayar'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Belum dibayar</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 text-center">
                <p class="text-3xl font-extrabold text-green-600">{{ $ringkasan['dikonfirmasi'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Dikonfirmasi</p>
            </div>
        </div>

        {{-- Booking aktif + informasi pembayaran --}}
        <div class="mt-8">
            <h2 class="text-lg font-bold text-slate-900 mb-3">Booking Aktif</h2>
            <div class="space-y-4">
                @forelse($aktif as $b)
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-xs text-slate-400">{{ $b->kode_booking }}</p>
                                <h3 class="font-bold text-slate-900 text-lg">{{ $b->paket->nama_paket }}</h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Guide: {{ $b->guide->nama_guide }} &middot;
                                    {{ $b->tanggal_booking->format('d M Y') }} &middot;
                                    {{ $b->jumlah_peserta }} peserta
                                </p>
                            </div>
                            @include('partials.badge-status', ['status' => $b->status])
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100 grid sm:grid-cols-3 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-slate-400">Total pembayaran</p>
                                <p class="font-bold text-slate-900">Rp {{ number_format($b->total_bayar, 0, ',', '.') }}</p>
                            </div>
                            @if($b->status === 'belum_bayar')
                                <div>
                                    <p class="text-xs text-slate-400">Kode virtual account</p>
                                    <p class="font-mono font-bold text-brand-blue">{{ $b->kode_va ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Bayar sebelum</p>
                                    <p class="font-semibold text-slate-900">{{ $b->batas_bayar ? $b->batas_bayar->format('d M Y H:i') : '-' }}</p>
                                </div>
                            @else
                                <div>
                                    <p class="text-xs text-slate-400">Dibayar pada</p>
                                    <p class="font-semibold text-slate-900">{{ $b->tanggal_bayar ? $b->tanggal_bayar->format('d M Y H:i') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Keterangan</p>
                                    <p class="font-semibold text-slate-900">
                                        {{ $b->status === 'sudah_bayar' ? 'Menunggu konfirmasi admin' : 'Bukti bayar dikirim ke email' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-8 text-center text-slate-500 border border-slate-100">
                        Belum ada booking aktif.
                        <a href="{{ route('home') }}#paket" class="text-brand-blue font-semibold underline">Pilih paket wisata</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Riwayat pemesanan --}}
        <div class="mt-8">
            <h2 class="text-lg font-bold text-slate-900 mb-3">Riwayat Pemesanan</h2>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                            <tr>
                                <th class="text-left px-5 py-3">Kode</th>
                                <th class="text-left px-5 py-3">Paket</th>
                                <th class="text-left px-5 py-3">Guide</th>
                                <th class="text-left px-5 py-3">Tanggal</th>
                                <th class="text-right px-5 py-3">Total</th>
                                <th class="text-center px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($riwayat as $b)
                                <tr>
                                    <td class="px-5 py-3 font-mono text-xs">{{ $b->kode_booking }}</td>
                                    <td class="px-5 py-3">{{ $b->paket->nama_paket }}</td>
                                    <td class="px-5 py-3">{{ $b->guide->nama_guide }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap">{{ $b->tanggal_booking->format('d M Y') }}</td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">Rp {{ number_format($b->total_bayar, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-center">@include('partials.badge-status', ['status' => $b->status])</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada riwayat pemesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
