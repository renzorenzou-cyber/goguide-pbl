@extends('layouts.site')

@section('title', $paket->nama_paket . ' - GoGuide')

@section('content')
@php
    $besok   = now()->addDay()->toDateString();
    $tanggal = request('tanggal');
@endphp

<section class="py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <a href="{{ route('home') }}#paket" class="text-sm text-brand-blue hover:underline">&larr; Semua paket</a>

        <div class="mt-4 grid gap-8 lg:grid-cols-3">

            {{-- Info paket --}}
            <div class="lg:col-span-2">
                <div class="h-64 rounded-2xl overflow-hidden bg-gradient-to-br from-sky-300 via-sky-200 to-emerald-200">
                    @if($paket->foto_url)
                        <img src="{{ $paket->foto_url }}" alt="{{ $paket->nama_paket }}" class="w-full h-full object-cover">
                    @endif
                </div>

                <h1 class="text-3xl font-extrabold text-slate-900 mt-6">{{ $paket->nama_paket }}</h1>
                <p class="text-sm text-slate-500 mt-1"><i class="fa-regular fa-clock mr-1"></i> {{ $paket->durasi }}</p>
                <p class="text-slate-600 mt-4">{{ $paket->deskripsi }}</p>

                <h2 class="font-bold text-lg text-slate-900 mt-8">Objek wisata yang dikunjungi</h2>
                <div class="mt-3 space-y-3">
                    @foreach($paket->objek as $o)
                        <div class="bg-white rounded-xl border border-slate-100 p-4 flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-brand-blue text-white flex items-center justify-center text-sm font-extrabold shrink-0">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">{{ $o->nama_objek }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $o->kategori->nama_kategori }} &middot; {{ $o->area->nama_area }}, {{ $o->area->kabupaten }}
                                </p>
                                @if($o->deskripsi)
                                    <p class="text-sm text-slate-600 mt-1">{{ $o->deskripsi }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Ringkasan harga --}}
            <div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 lg:sticky lg:top-24">
                    <span class="text-xs text-slate-400">Harga</span>
                    <p class="text-2xl font-extrabold text-brand-blue">
                        Rp {{ number_format($paket->harga_paket, 0, ',', '.') }}
                        <span class="text-sm font-normal text-slate-500">/ peserta</span>
                    </p>
                    <p class="text-xs text-slate-500 mt-2">{{ $guides->count() }} guide menangani paket ini.</p>
                    <a href="#cek" class="block text-center mt-4 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm">
                        Pilih guide dan tanggal
                    </a>
                </div>
            </div>
        </div>

        {{-- Pilih guide dan tanggal --}}
        <div id="cek" class="mt-12">
            <h2 class="text-2xl font-extrabold text-slate-900">Pilih Guide dan Tanggal</h2>

            @if($guides->isEmpty())
                <p class="mt-4 text-slate-500">Belum ada guide yang menangani paket ini.</p>
            @else
                <form method="GET" action="{{ route('paket.show', $paket->id_paket) }}#cek" class="mt-5">
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($guides as $g)
                            @php
                                $inisial = collect(explode(' ', $g->nama_guide))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
                            @endphp
                            <label class="block cursor-pointer">
                                <input type="radio" name="guide" value="{{ $g->id_guide }}" class="peer sr-only"
                                       @checked((int) request('guide') === $g->id_guide)>
                                <div class="bg-white rounded-2xl border-2 border-slate-100 p-5 transition-all peer-checked:border-brand-orange peer-checked:shadow-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-brand-blue text-white flex items-center justify-center font-extrabold shrink-0">{{ $inisial }}</div>
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $g->nama_guide }}</p>
                                            <p class="text-xs text-slate-500"><i class="fa-solid fa-star text-amber-400"></i> {{ number_format($g->rating, 1) }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-3 line-clamp-3">{{ $g->bio_singkat }}</p>

                                    <p class="text-xs font-semibold text-slate-700 mt-3">Tanggal tersedia terdekat</p>
                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        @forelse($g->tanggal_terdekat as $t)
                                            <a href="{{ route('paket.show', ['paket' => $paket->id_paket, 'guide' => $g->id_guide, 'tanggal' => $t]) }}#cek"
                                               class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-brand-blue hover:text-white text-[11px] font-semibold text-slate-600">
                                                {{ \Carbon\Carbon::parse($t)->format('d M') }}
                                            </a>
                                        @empty
                                            <span class="text-xs text-slate-400">Belum ada jadwal.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row sm:items-end gap-3">
                        <div>
                            <label for="tanggal" class="text-sm font-semibold text-slate-700">Tanggal booking</label>
                            <input id="tanggal" type="date" name="tanggal" min="{{ $besok }}" value="{{ $tanggal }}"
                                   class="mt-1 block rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-blue focus:ring-brand-blue">
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-darkBlue text-white font-bold text-sm">
                            <i class="fa-solid fa-calendar-check mr-1"></i> Cek ketersediaan
                        </button>
                    </div>
                </form>

                {{-- Hasil pengecekan --}}
                @if($hasil)
                    <div class="mt-5 rounded-2xl px-4 py-3 text-sm {{ $hasil[0] ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' }}">
                        <i class="fa-solid {{ $hasil[0] ? 'fa-circle-check' : 'fa-circle-xmark' }} mr-1"></i>
                        {{ $hasil[1] }}
                    </div>

                    @if($hasil[0] && $guidePilihan)
                        <div class="mt-4 bg-white rounded-2xl border border-slate-100 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="text-sm text-slate-600">
                                <p class="font-bold text-slate-900">{{ $paket->nama_paket }}</p>
                                <p>Guide: {{ $guidePilihan->nama_guide }}</p>
                                <p>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</p>
                            </div>
                            @if(Route::has('booking.create'))
                                <a href="{{ route('booking.create', ['paket' => $paket->id_paket, 'guide' => $guidePilihan->id_guide, 'tanggal' => $tanggal]) }}"
                                   class="px-6 py-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm text-center">
                                    Booking
                                </a>
                            @else
                                <span class="px-6 py-3 rounded-xl bg-slate-200 text-slate-500 font-bold text-sm text-center">
                                    Booking (tahap 2)
                                </span>
                            @endif
                        </div>
                    @endif
                @endif
            @endif
        </div>

    </div>
</section>
@endsection