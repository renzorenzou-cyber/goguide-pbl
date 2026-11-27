@extends('layouts.site')

@section('title', 'Ulasan Saya - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <span class="text-brand-orange font-bold text-sm uppercase tracking-wider">Ulasan</span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Ulasan Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Beri rating untuk guide dari tour yang sudah selesai.</p>

        @include('admin.partials.flash')

        @if ($menunggu->isEmpty() && $sudah->isEmpty())
            <div class="mt-8 bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                <p class="font-semibold text-slate-700">Belum ada tour yang selesai.</p>
                <p class="text-sm text-slate-500 mt-1">Rating bisa diberikan setelah tour kamu ditandai selesai oleh admin.</p>
                <a href="{{ route('bookings.index') }}" class="inline-block mt-4 text-sm font-bold text-brand-orange hover:underline">Lihat booking saya</a>
            </div>
        @else
            {{-- Menunggu rating --}}
            <h2 class="mt-8 font-bold text-lg text-slate-900">Menunggu rating <span class="text-sm font-semibold text-slate-400">({{ $menunggu->count() }})</span></h2>
            @if ($menunggu->isEmpty())
                <p class="mt-2 text-sm text-slate-500">Semua tour sudah kamu beri rating. Terima kasih!</p>
            @else
                <div class="mt-3 space-y-3">
                    @foreach ($menunggu as $b)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-wrap items-center gap-4">
                            @if ($b->guide?->foto_url)
                                <img src="{{ $b->guide->foto_url }}" alt="{{ $b->guide->nama_guide }}" class="w-14 h-14 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-full bg-brand-blue text-white flex items-center justify-center font-extrabold shrink-0">{{ $b->guide?->inisial }}</div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-900">{{ $b->paket?->nama_paket }}</p>
                                <p class="text-sm text-slate-600">Guide {{ $b->guide?->nama_guide }} &middot; {{ $b->tanggal_booking->locale('id')->translatedFormat('d M Y') }}</p>
                            </div>
                            <a href="{{ route('ulasan.create', $b->id_booking) }}" class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white text-sm font-bold shadow">
                                &#9733; Beri rating
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Sudah dirating --}}
            @if ($sudah->isNotEmpty())
                <h2 class="mt-10 font-bold text-lg text-slate-900">Sudah dirating <span class="text-sm font-semibold text-slate-400">({{ $sudah->count() }})</span></h2>
                <div class="mt-3 space-y-3">
                    @foreach ($sudah as $b)
                        <div class="bg-white rounded-2xl border border-slate-100 p-5">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900">{{ $b->paket?->nama_paket }}</p>
                                    <p class="text-sm text-slate-600">Guide {{ $b->guide?->nama_guide }} &middot; {{ $b->tanggal_booking->locale('id')->translatedFormat('d M Y') }}</p>
                                </div>
                                @include('partials.bintang', ['nilai' => $b->ulasan->rating])
                            </div>
                            @if ($b->ulasan->komentar)
                                <p class="mt-2 text-sm text-slate-600">{!! nl2br(e($b->ulasan->komentar)) !!}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</section>
@endsection