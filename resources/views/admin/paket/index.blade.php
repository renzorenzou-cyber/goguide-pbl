@extends('layouts.site')

@section('title', 'Kelola Paket Wisata - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard admin
        </a>

        <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Paket wisata</h1>
                <p class="text-sm text-slate-500 mt-1">Paket aktif tampil sebagai card di halaman utama.</p>
            </div>
            <a href="{{ route('admin.paket.create') }}"
               class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm shadow">
                <i class="fa-solid fa-plus mr-1"></i> Tambah paket
            </a>
        </div>

        @include('admin.partials.destinasi-tabs')

        @if (session('success'))
            <div role="status" class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">{{ session('error') }}</div>
        @endif

        <form method="GET" class="mt-6 flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama paket"
                   class="w-full max-w-sm rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">
            <button class="px-4 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">Cari</button>
        </form>

        @if ($pakets->isEmpty())
            <div class="mt-6 bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                <p class="text-slate-700 font-semibold">
                    {{ request('q') ? 'Tidak ada paket yang cocok dengan pencarian itu.' : 'Belum ada paket wisata.' }}
                </p>
                <a href="{{ route('admin.paket.create') }}" class="inline-block mt-3 text-sm font-bold text-brand-orange hover:underline">Buat paket pertama</a>
            </div>
        @else
            <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($pakets as $p)
                    <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
                        @if ($p->foto_url)
                            <img src="{{ $p->foto_url }}" alt="Foto {{ $p->nama_paket }}" class="w-full aspect-[4/3] object-cover">
                        @else
                            <div class="w-full aspect-[4/3] bg-slate-100 text-slate-400 flex items-center justify-center text-sm">
                                <i class="fa-regular fa-image mr-2"></i> Belum ada foto
                            </div>
                        @endif

                        <div class="p-4 flex flex-col gap-1.5 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h2 class="font-bold text-slate-900 leading-snug">{{ $p->nama_paket }}</h2>
                                <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-bold
                                    {{ $p->status === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $p->status }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-600">
                                <span class="font-bold text-brand-blue">Rp{{ number_format($p->harga_paket, 0, ',', '.') }}</span> per peserta &middot; {{ $p->durasi }}
                            </p>
                            @php($areas = $p->objek->map(fn ($o) => $o->area?->nama_area)->filter()->unique()->implode(', '))
                            <p class="text-xs text-slate-500">
                                <i class="fa-solid fa-location-dot text-brand-orange"></i>
                                {{ $areas ?: 'Belum ada objek wisata' }}
                            </p>
                            <p class="text-xs text-slate-500">Fee guide {{ $p->persen_fee_guide }}% &middot; {{ $p->booking_count }} booking</p>

                            <div class="mt-auto pt-3 flex flex-wrap gap-2">
                                <a href="{{ route('admin.paket.edit', $p->id_paket) }}"
                                   class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm font-semibold hover:border-brand-blue hover:text-brand-blue">Edit</a>

                                <form method="POST" action="{{ route('admin.paket.toggle', $p->id_paket) }}">
                                    @csrf @method('PATCH')
                                    <button class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">
                                        {{ $p->status === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.paket.destroy', $p->id_paket) }}"
                                      onsubmit="return confirm('Hapus paket ini? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg border border-red-200 text-sm font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">{{ $pakets->links('admin.partials.pagination') }}</div>
        @endif
    </div>
</section>
@endsection