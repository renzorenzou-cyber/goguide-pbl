@extends('layouts.site')

@section('title', 'Paket yang Ditangani - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <a href="{{ route('guide.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Paket yang Ditangani</h1>
        <p class="text-sm text-slate-500 mt-1">Centang paket wisata yang bisa kamu handle. Wisatawan hanya melihat kamu pada paket yang dicentang.</p>

        @include('admin.partials.flash')

        <form method="POST" action="{{ route('guide.paket.update') }}" class="mt-6">
            @csrf
            @method('PUT')

            <div class="grid sm:grid-cols-2 gap-4">
                @forelse($pakets as $p)
                    <label class="flex gap-3 bg-white rounded-2xl border border-slate-200 p-4 cursor-pointer hover:border-brand-orange has-[:checked]:border-brand-orange has-[:checked]:bg-orange-50/40">
                        <input type="checkbox" name="paket[]" value="{{ $p->id_paket }}" @checked(in_array($p->id_paket, old('paket', $dipilih)))
                               class="mt-1 rounded border-slate-300 text-brand-orange focus:ring-brand-orange">
                        <span class="min-w-0">
                            <span class="block font-bold text-slate-900">{{ $p->nama_paket }}</span>
                            <span class="block text-xs text-slate-500 mt-0.5">
                                Rp {{ number_format($p->harga_paket, 0, ',', '.') }} / peserta
                                @if($p->durasi) &middot; {{ $p->durasi }} @endif
                            </span>
                            @if($p->objek->isNotEmpty())
                                <span class="block text-xs text-slate-400 mt-1">{{ $p->objek->pluck('nama_objek')->take(3)->implode(', ') }}</span>
                            @endif
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-slate-500 sm:col-span-2">Belum ada paket aktif. Hubungi admin.</p>
                @endforelse
            </div>

            @if($pakets->isNotEmpty())
                <div class="flex justify-end mt-6">
                    <button class="px-6 py-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm shadow">Simpan paket</button>
                </div>
            @endif
        </form>
    </div>
</section>
@endsection