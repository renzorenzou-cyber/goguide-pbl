@extends('layouts.site')

@section('title', $tour->title . ' - Go Go Guide')

@section('content')
@php
    $gallery   = $tour->gallery;
    $guide     = $tour->guide;
    $guideName = $guide->user->name;
    $firstName = str($guideName)->before(' ');
@endphp

<div class="max-w-6xl mx-auto px-4 py-10">

    <a href="{{ route('home') }}" class="text-sm text-brand-blue hover:underline">&larr; Kembali</a>

    <div class="mt-4 grid gap-8 md:grid-cols-3">

        {{-- Kiri: galeri + info tour + guide --}}
        <div class="md:col-span-2">

            {{-- GALERI FOTO --}}
            @if($gallery->isNotEmpty())
                <div x-data="{
                        imgs: @js($gallery),
                        i: 0,
                        open: false,
                        next() { this.i = (this.i + 1) % this.imgs.length },
                        prev() { this.i = (this.i - 1 + this.imgs.length) % this.imgs.length }
                     }"
                     @keydown.escape.window="open = false"
                     @keydown.right.window="open && next()"
                     @keydown.left.window="open && prev()">

                    {{-- Foto besar (klik buat zoom) --}}
                    <button type="button" @click="open = true"
                            class="relative block w-full h-72 md:h-[26rem] rounded-2xl overflow-hidden bg-slate-200 group">
                        <img :src="imgs[i].url" :alt="imgs[i].caption || ''"
                             class="w-full h-full object-cover transition duration-300 group-hover:scale-[1.02]">

                        <div x-show="imgs[i].caption" x-text="imgs[i].caption"
                             class="absolute inset-x-0 bottom-0 p-4 pt-10 text-left text-white text-sm bg-gradient-to-t from-black/60 to-transparent"></div>

                        <span class="absolute top-3 right-3 bg-black/60 text-white text-xs px-3 py-1 rounded-full">
                            <i class="fa-solid fa-images mr-1"></i>
                            <span x-text="(i + 1) + ' / ' + imgs.length"></span>
                        </span>
                    </button>

                    {{-- Thumbnail (cuma muncul kalau fotonya lebih dari 1) --}}
                    <div x-show="imgs.length > 1" class="flex gap-2 mt-3 overflow-x-auto pb-1">
                        <template x-for="(img, idx) in imgs" :key="idx">
                            <button type="button" @click="i = idx"
                                    class="shrink-0 w-24 h-16 rounded-lg overflow-hidden border-2 transition"
                                    :class="i === idx ? 'border-brand-orange' : 'border-transparent opacity-70 hover:opacity-100'">
                                <img :src="img.url" alt="" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>

                    {{-- Lightbox --}}
                    <div x-show="open" x-cloak x-transition.opacity
                         class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
                         @click.self="open = false">

                        <button type="button" @click="open = false" aria-label="Tutup"
                                class="absolute top-4 right-5 text-white text-4xl leading-none">&times;</button>

                        <button type="button" x-show="imgs.length > 1" @click.stop="prev()" aria-label="Sebelumnya"
                                class="absolute left-3 md:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <figure class="max-w-5xl w-full">
                            <img :src="imgs[i].url" :alt="imgs[i].caption || ''"
                                 class="max-h-[80vh] w-auto max-w-full mx-auto rounded-lg">
                            <figcaption class="text-center text-white/80 text-sm mt-3" x-text="imgs[i].caption"></figcaption>
                        </figure>

                        <button type="button" x-show="imgs.length > 1" @click.stop="next()" aria-label="Berikutnya"
                                class="absolute right-3 md:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            @else
                <div class="h-72 md:h-[26rem] rounded-2xl bg-slate-200 flex items-center justify-center text-slate-400">
                    <i class="fa-regular fa-image text-5xl"></i>
                </div>
            @endif

            {{-- INFO TOUR --}}
            <p class="text-brand-orange text-xs font-semibold uppercase mt-6">{{ $tour->region->name }}</p>
            <h1 class="text-3xl font-bold mt-1">{{ $tour->title }}</h1>

            <div class="flex gap-4 text-sm text-slate-500 mt-2">
                <span>⭐ {{ $tour->rating }} ({{ $tour->review_count }} review)</span>
                <span>⏱ {{ $tour->duration }}</span>
            </div>

            <h2 class="font-bold text-lg mt-8">Tentang tour ini</h2>
            <p class="text-slate-600 mt-2">{{ $tour->description }}</p>

            {{-- KARTU GUIDE --}}
            <h2 class="font-bold text-lg mt-10">Guide kamu</h2>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mt-3">

                <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                    @include('guides.partials.avatar', ['guide' => $guide, 'size' => 'w-24 h-24 text-2xl'])

                    <div class="min-w-0">
                        <a href="{{ route('guides.show', $guide) }}"
                           class="text-xl font-bold hover:text-brand-blue">{{ $guideName }}</a>

                        @if($guide->specialty)
                            <p class="text-sm text-brand-blue font-semibold">{{ $guide->specialty }}</p>
                        @endif

                        @if($guide->base_location)
                            <p class="text-sm text-slate-500 mt-1">
                                <i class="fa-solid fa-location-dot text-brand-orange mr-1"></i>{{ $guide->base_location }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-6 text-center">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-lg font-extrabold text-brand-blue">⭐ {{ number_format($guide->rating, 1) }}</p>
                        <p class="text-xs text-slate-500">{{ $guide->review_count }} review</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-lg font-extrabold text-brand-blue">
                            {{ $guide->experience_years ? $guide->experience_years . ' th' : '-' }}
                        </p>
                        <p class="text-xs text-slate-500">Pengalaman</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-lg font-extrabold text-brand-blue">{{ $guide->tours->count() }}</p>
                        <p class="text-xs text-slate-500">Tour aktif</p>
                    </div>
                </div>

                @if(count($guide->language_list))
                    <div class="flex flex-wrap items-center gap-2 mt-5">
                        <span class="text-sm text-slate-500">Bahasa:</span>
                        @foreach($guide->language_list as $lang)
                            <span class="text-xs font-semibold bg-brand-blue/10 text-brand-blue px-3 py-1 rounded-full">{{ $lang }}</span>
                        @endforeach
                    </div>
                @endif

                @if($guide->bio)
                    <p class="text-sm text-slate-600 mt-4 leading-relaxed">{!! nl2br(e($guide->bio)) !!}</p>
                @endif

                @if($otherTours->isNotEmpty())
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <p class="text-sm font-semibold mb-2">Tour lain dari {{ $firstName }}</p>
                        <ul class="space-y-1">
                            @foreach($otherTours as $t)
                                <li>
                                    <a href="{{ route('tours.show', $t->slug) }}"
                                       class="text-sm text-brand-blue hover:underline">{{ $t->title }}</a>
                                    <span class="text-xs text-slate-400">· {{ $t->region->name ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <a href="{{ route('guides.show', $guide) }}"
                   class="inline-block mt-5 text-sm font-semibold text-brand-orange hover:underline">
                    Lihat profil lengkap {{ $firstName }} &rarr;
                </a>
            </div>
        </div>

        {{-- Kanan: kartu harga (tidak diubah) --}}
        <div>
            <div class="bg-white rounded-xl shadow p-5 sticky top-24"
                 x-data="{ price: {{ $tour->price }}, qty: {{ old('participants', 1) }} }">

                <span class="text-xs text-slate-400">Mulai dari</span>
                <p class="text-2xl font-bold">Rp {{ number_format($tour->price, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400">per orang</p>

                @auth
                    <form method="POST" action="{{ route('bookings.store', $tour->slug) }}" class="mt-4 space-y-3">
                        @csrf

                        <div>
                            <label class="text-sm font-medium">Tanggal tour</label>
                            <input type="date" name="tour_date"
                                   value="{{ old('tour_date') }}"
                                   min="{{ now()->toDateString() }}"
                                   class="w-full border rounded px-3 py-2 text-sm mt-1" required>
                            @error('tour_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">Jumlah orang</label>
                            <input type="number" name="participants" x-model.number="qty"
                                   min="1" max="20"
                                   class="w-full border rounded px-3 py-2 text-sm mt-1" required>
                            @error('participants')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">Catatan (opsional)</label>
                            <textarea name="notes" rows="2"
                                      class="w-full border rounded px-3 py-2 text-sm mt-1">{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex justify-between border-t pt-3">
                            <span class="text-sm text-slate-500">Total</span>
                            <span class="font-bold" x-text="'Rp ' + (price * (qty || 0)).toLocaleString('id-ID')"></span>
                        </div>

                        <button type="submit" class="w-full bg-brand-blue text-white py-2 rounded hover:bg-brand-darkBlue">
                            Book Sekarang
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="block text-center w-full mt-4 bg-brand-blue text-white py-2 rounded hover:bg-brand-darkBlue">
                        Login untuk Booking
                    </a>
                    <p class="text-xs text-slate-400 mt-2 text-center">
                        Belum punya akun? <a href="{{ route('register') }}" class="underline">Daftar</a>
                    </p>
                @endauth
            </div>
        </div>

    </div>
</div>
@endsection