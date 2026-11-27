@extends('layouts.site')

@section('title', 'Beri Rating - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        <a href="{{ route('ulasan.index') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Ulasan saya
        </a>

        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Beri rating untuk guide</h1>

        @if (session('error'))
            <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">{{ session('error') }}</div>
        @endif

        {{-- Ringkasan tour --}}
        <div class="mt-6 bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
            @if ($booking->guide?->foto_url)
                <img src="{{ $booking->guide->foto_url }}" alt="{{ $booking->guide->nama_guide }}" class="w-16 h-16 rounded-full object-cover shrink-0">
            @else
                <div class="w-16 h-16 rounded-full bg-brand-blue text-white flex items-center justify-center text-xl font-extrabold shrink-0">{{ $booking->guide?->inisial }}</div>
            @endif
            <div class="min-w-0">
                <p class="font-bold text-slate-900 text-lg">{{ $booking->guide?->nama_guide }}</p>
                <p class="text-sm text-slate-600">{{ $booking->paket?->nama_paket }}</p>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $booking->tanggal_booking->locale('id')->translatedFormat('l, d F Y') }} &middot; {{ $booking->kode_booking }}
                </p>
            </div>
        </div>

        <div class="mt-4 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
            <p class="text-sm text-slate-500">Bagaimana tour kamu? Penilaianmu membantu wisatawan lain memilih guide. Ulasan tidak bisa diubah setelah dikirim.</p>
            <div class="mt-4">
                @include('partials.form-ulasan', ['dari' => 'ulasan'])
            </div>
        </div>
    </div>
</section>
@endsection