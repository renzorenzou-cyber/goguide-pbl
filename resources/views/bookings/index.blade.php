@extends('layouts.site')

@section('title', 'Booking Saya - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Beranda
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Booking Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Riwayat dan status semua pemesanan tour kamu.</p>

        @if (session('success'))
            <div role="status" class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('success') }}</div>
        @endif

        @if ($bookings->isEmpty())
            <div class="mt-6 bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                <p class="font-semibold text-slate-700">Belum ada booking.</p>
                <a href="{{ route('paket.index') }}" class="inline-block mt-3 text-sm font-bold text-brand-orange hover:underline">Cari paket wisata</a>
            </div>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($bookings as $b)
                    <a href="{{ route('bookings.show', $b->id_booking) }}"
                       class="block bg-white rounded-2xl border border-slate-200 p-5 hover:border-brand-orange hover:shadow-md transition">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-mono text-slate-500">{{ $b->kode_booking }}</p>
                                <h2 class="font-bold text-slate-900">{{ $b->paket?->nama_paket }}</h2>
                                <p class="text-sm text-slate-600 mt-1">
                                    <i class="fa-regular fa-calendar mr-1"></i>{{ $b->tanggal_booking->translatedFormat('d M Y') }}
                                    &middot; {{ substr($b->jam_mulai, 0, 5) }}
                                    &middot; Guide {{ $b->guide?->nama_guide }}
                                </p>
                            </div>
                            <div class="text-right">
                                @include('partials.badge-status', ['status' => $b->status])
                                <p class="mt-2 font-extrabold text-brand-blue">Rp{{ number_format($b->total_bayar, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @if ($b->status === 'belum_bayar' && $b->batas_bayar)
                            <p class="mt-3 text-xs font-semibold text-amber-700">
                                Bayar sebelum {{ $b->batas_bayar->translatedFormat('d M Y H:i') }}
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $bookings->links('admin.partials.pagination') }}</div>
        @endif
    </div>
</section>
@endsection