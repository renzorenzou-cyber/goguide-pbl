@extends('layouts.site')

@section('title', 'Dashboard Pemandu - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        {{-- Profil pemandu --}}
        <div class="rounded-3xl p-6 sm:p-8 text-white" style="background: linear-gradient(135deg, #065f46 0%, #059669 100%);">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                @if($user->photo_url)
                    <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover shrink-0" style="box-shadow: 0 0 0 4px rgba(255,255,255,.9);">
                @else
                    <div class="w-20 h-20 rounded-full shrink-0 flex items-center justify-center text-2xl font-extrabold bg-white/15" style="box-shadow: 0 0 0 4px rgba(255,255,255,.35);">{{ $user->initials }}</div>
                @endif
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/15 border border-white/25">
                        <i class="fa-solid fa-map-location-dot"></i> Pemandu Wisata
                    </span>
                    <h1 class="text-2xl font-extrabold mt-2 truncate">Halo, {{ $user->name }}</h1>
                    <p class="text-sm text-emerald-50 truncate">{{ $user->email }}@if($user->username) &middot; {{ '@' . $user->username }}@endif</p>
                </div>
            </div>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('profile.edit') }}" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/30 font-bold text-sm">
                    <i class="fa-solid fa-user-pen mr-1"></i> Edit Profil dan Password
                </a>
                @if($guide)
    <a href="{{ route('guide.paket') }}" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/30 font-bold text-sm">
        <i class="fa-solid fa-suitcase-rolling mr-1"></i> Kelola Paket
    </a>
    <a href="{{ route('guide.jadwal') }}" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/30 font-bold text-sm">
        <i class="fa-solid fa-calendar-days mr-1"></i> Kelola Jadwal
    </a>
@endif
            </div>
        </div>
        @include('admin.partials.flash')

@if($guide && ($paket->isEmpty() || $jadwal->isEmpty()))
    <div class="mt-5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm px-5 py-4">
        <i class="fa-solid fa-circle-info mr-1"></i>
        Kamu baru tampil kepada wisatawan setelah memilih minimal satu
        <a href="{{ route('guide.paket') }}" class="font-bold underline">paket</a> dan mengisi
        <a href="{{ route('guide.jadwal') }}" class="font-bold underline">jadwal layanan</a>.
    </div>
@endif

        @if($user->wajib_ganti_password)
            <div class="mt-5 rounded-2xl bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm px-5 py-4">
                <i class="fa-solid fa-key mr-1"></i>
                Password kamu masih password sementara. Segera ganti lewat
                <a href="{{ route('profile.edit') }}" class="font-bold underline">Edit Profil</a>.
            </div>
        @endif

        @if(! $guide)
            <div class="mt-6 rounded-2xl bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm px-5 py-4">
                Data pemandu untuk akun ini belum terhubung. Hubungi admin.
            </div>
        @else

        @php
            $badgeVerifikasi = [
                'Menunggu'      => ['Menunggu verifikasi', 'bg-yellow-100 text-yellow-700'],
                'Terverifikasi' => ['Terverifikasi', 'bg-green-100 text-green-700'],
                'Ditolak'       => ['Ditolak', 'bg-red-100 text-red-700'],
            ];
            [$lblV, $clsV] = $badgeVerifikasi[$guide->status_verifikasi] ?? [$guide->status_verifikasi, 'bg-slate-100 text-slate-700'];
        @endphp

        {{-- Ringkasan --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Status verifikasi akun</p>
                <p class="mt-2"><span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $clsV }}">{{ $lblV }}</span></p>
                @if($guide->status_verifikasi === 'Ditolak' && $guide->alasan_penolakan)
                    <p class="text-xs text-red-600 mt-2">{{ $guide->alasan_penolakan }}</p>
                @endif
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Booking masuk</p>
                <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $masuk->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Tanggal aktif bulan ini</p>
                <p class="text-3xl font-extrabold text-brand-blue mt-1">{{ $aktifBulanIni }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Paket yang ditangani</p>
                <p class="text-3xl font-extrabold text-brand-orange mt-1">{{ $paket->count() }}</p>
            </div>
        </div>

                {{-- Fee --}}
        <div class="grid sm:grid-cols-3 gap-4 mt-4">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Perkiraan fee tour mendatang</p>
                <p class="text-2xl font-extrabold text-brand-blue mt-1">Rp {{ number_format($fee['perkiraan'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Dari booking yang sudah dikonfirmasi.</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Fee menunggu dibayar</p>
                <p class="text-2xl font-extrabold text-yellow-500 mt-1">Rp {{ number_format($fee['belum'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Tour sudah selesai, fee belum ditransfer admin.</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <p class="text-xs text-slate-500">Fee sudah diterima</p>
                <p class="text-2xl font-extrabold text-green-600 mt-1">Rp {{ number_format($fee['sudah'], 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Booking masuk --}}
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Booking Masuk</h2>
                <p class="text-xs text-slate-500">Booking yang sudah dikonfirmasi admin dan akan datang.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Tanggal</th>
                            <th class="text-left px-5 py-3">Paket</th>
                            <th class="text-left px-5 py-3">Wisatawan</th>
                            <th class="text-left px-5 py-3">Kontak</th>
                            <th class="text-center px-5 py-3">Peserta</th>
                            <th class="text-left px-5 py-3">Catatan</th>
                            <th class="text-right px-5 py-3">Fee</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($masuk as $b)
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap font-semibold">{{ $b->tanggal_booking->format('d M Y') }}</td>
                                <td class="px-5 py-3">{{ $b->paket->nama_paket }}</td>
                                <td class="px-5 py-3">{{ $b->wisatawan->nama_wisatawan }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $b->wisatawan->telepon ?: '-' }}</td>
                                <td class="px-5 py-3 text-center">{{ $b->jumlah_peserta }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $b->catatan ?: '-' }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap font-semibold text-emerald-700">Rp {{ number_format($b->fee_guide, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada booking masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 mt-8">
            {{-- Jadwal layanan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h2 class="font-bold text-slate-900">Jadwal Layanan</h2>
                <p class="text-xs text-slate-500 mb-3">Tanggal aktif yang akan datang.</p>
                <div class="flex flex-wrap gap-2">
                    @forelse($jadwal as $j)
                        @php $terpesan = in_array($j->tanggal_aktif->toDateString(), $tanggalTerpesan, true); @endphp
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $terpesan ? 'bg-brand-orange text-white' : 'bg-emerald-50 text-emerald-700' }}">
                            {{ $j->tanggal_aktif->format('d M') }}{{ $terpesan ? ' (dipesan)' : '' }}
                        </span>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada tanggal aktif.</p>
                    @endforelse
                </div>
            </div>

            {{-- Paket yang ditangani --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h2 class="font-bold text-slate-900">Paket yang Ditangani</h2>
                <p class="text-xs text-slate-500 mb-3">Paket wisata yang dapat kamu handle.</p>
                <ul class="space-y-2">
                    @forelse($paket as $p)
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-slate-800">{{ $p->nama_paket }}</span>
                            <span class="text-xs text-slate-500">Rp {{ number_format($p->harga_paket, 0, ',', '.') }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">Belum memilih paket.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Riwayat booking --}}
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Riwayat Booking</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Tanggal</th>
                            <th class="text-left px-5 py-3">Paket</th>
                            <th class="text-left px-5 py-3">Wisatawan</th>
                            <th class="text-center px-5 py-3">Peserta</th>
                            <th class="text-center px-5 py-3">Status</th>
                            <th class="text-right px-5 py-3">Fee</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayat as $b)
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $b->tanggal_booking->format('d M Y') }}</td>
                                <td class="px-5 py-3">{{ $b->paket->nama_paket }}</td>
                                <td class="px-5 py-3">{{ $b->wisatawan->nama_wisatawan }}</td>
                                <td class="px-5 py-3 text-center">{{ $b->jumlah_peserta }}</td>
                                <td class="px-5 py-3 text-center">@include('partials.badge-status', ['status' => $b->status])</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                 @if ($b->status === 'selesai')
                                 <span class="font-semibold">Rp {{ number_format($b->fee_guide, 0, ',', '.') }}</span>
                                 <p class="text-[11px] font-bold {{ $b->status_bayar_guide === 'sudah' ? 'text-green-600' : 'text-yellow-600' }}">
                                 {{ $b->status_bayar_guide === 'sudah' ? 'Sudah dibayar' : 'Belum dibayar' }}
                                </p>
                                 @else
                                 <span class="text-slate-400">-</span>
                                 @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada riwayat booking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @endif

    </div>
</section>
@endsection
