@extends('layouts.site')

@section('title', 'Kelola Booking - Go Guide')

@section('content')
@php
    $tabs = [
        'sudah_bayar'  => 'Perlu verifikasi',
        'belum_bayar'  => 'Belum bayar',
        'dikonfirmasi' => 'Dikonfirmasi',
        'selesai'      => 'Selesai',
        'dibatalkan'   => 'Dibatalkan',
    ];
@endphp
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard admin
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Kelola Booking</h1>
        <p class="text-sm text-slate-500 mt-1">Verifikasi pembayaran, konfirmasi booking, dan tandai tour selesai.</p>

        @include('admin.partials.flash')

        <nav class="mt-5 flex flex-wrap gap-2" aria-label="Status booking">
            @foreach ($tabs as $kode => $label)
                <a href="{{ route('admin.booking.index', ['status' => $kode]) }}" @if($tab === $kode) aria-current="page" @endif
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition
                          {{ $tab === $kode ? 'bg-brand-blue text-white border-brand-blue' : 'bg-white text-slate-600 border-slate-200 hover:border-brand-orange hover:text-brand-orange' }}">
                    {{ $label }}
                    <span class="px-1.5 rounded-md text-[11px] {{ $tab === $kode ? 'bg-white/20' : 'bg-slate-100' }}">{{ $jumlah[$kode] ?? 0 }}</span>
                </a>
            @endforeach
        </nav>

        <form method="GET" class="mt-4 flex gap-2">
            <input type="hidden" name="status" value="{{ $tab }}">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kode booking"
                   class="w-full max-w-sm rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">
            <button class="px-4 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">Cari</button>
        </form>

        <div class="mt-5 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3">Booking</th>
                            <th class="text-left px-5 py-3">Wisatawan</th>
                            <th class="text-left px-5 py-3">Guide</th>
                            <th class="text-left px-5 py-3">Tour</th>
                            <th class="text-right px-5 py-3">Total</th>
                            <th class="text-left px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($bookings as $b)
                            <tr class="align-top">
                                <td class="px-5 py-3">
                                    <p class="font-mono text-xs">{{ $b->kode_booking }}</p>
                                    <p class="font-semibold">{{ $b->paket?->nama_paket }}</p>
                                    @if ($b->tanggal_bayar)
                                        <p class="text-xs text-slate-500">Dibayar {{ $b->tanggal_bayar->format('d M H:i') }} &middot; {{ $b->metode_pembayaran }}</p>
                                    @elseif ($b->kode_va)
                                        <p class="text-xs text-slate-500">VA {{ $b->kode_va }}</p>
                                    @endif
                                    @if ($b->status === 'dibatalkan' && $b->alasan_batal)
                                        <p class="text-xs text-red-600">{{ $b->alasan_batal }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3">{{ $b->wisatawan?->nama_wisatawan }}<p class="text-xs text-slate-500">{{ $b->wisatawan?->email }}</p></td>
                                <td class="px-5 py-3">{{ $b->guide?->nama_guide }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $b->tanggal_booking->format('d M Y') }}<p class="text-xs text-slate-500">{{ substr($b->jam_mulai, 0, 5) }} &middot; {{ $b->jumlah_peserta }} org</p></td>
                                <td class="px-5 py-3 text-right whitespace-nowrap font-semibold">Rp {{ number_format($b->total_bayar, 0, ',', '.') }}</td>
                                <td class="px-5 py-3">
                                    @if ($b->status === 'sudah_bayar')
                                        <form method="POST" action="{{ route('admin.booking.confirm', $b->id_booking) }}" onsubmit="return confirm('Pembayaran valid? Booking akan dikonfirmasi.')">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700">Konfirmasi</button>
                                        </form>
                                    @elseif ($b->status === 'dikonfirmasi' && ! $b->tanggal_booking->isFuture())
                                        <form method="POST" action="{{ route('admin.booking.finish', $b->id_booking) }}" onsubmit="return confirm('Tandai tour ini selesai?')">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-blue text-white hover:bg-brand-darkBlue">Tandai selesai</button>
                                        </form>
                                    @endif

                                    @if (in_array($b->status, ['sudah_bayar', 'dikonfirmasi'], true))
                                        <details class="mt-2">
                                            <summary class="cursor-pointer text-xs font-bold text-red-600">Tolak / batalkan</summary>
                                            <form method="POST" action="{{ route('admin.booking.reject', $b->id_booking) }}" class="mt-2 space-y-2">
                                                @csrf
                                                <input name="alasan" required minlength="5" maxlength="200" placeholder="Alasan (dikirim ke wisatawan)"
                                                       class="block w-56 rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs outline-none focus:border-brand-orange">
                                                <button class="px-3 py-1.5 rounded-lg text-xs font-bold border border-red-300 text-red-700 hover:bg-red-50">Batalkan booking</button>
                                            </form>
                                        </details>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">Tidak ada booking pada status ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $bookings->links('admin.partials.pagination') }}</div>
    </div>
</section>
@endsection