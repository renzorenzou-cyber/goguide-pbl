@extends('layouts.site')

@section('title', 'Kelola Objek Wisata - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard admin
        </a>

        <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Objek wisata</h1>
                <p class="text-sm text-slate-500 mt-1">Tempat yang dikunjungi di dalam paket. Area dan kategori diatur di tab masing-masing.</p>
            </div>
            <a href="{{ route('admin.objek.create') }}"
               class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm shadow">
                <i class="fa-solid fa-plus mr-1"></i> Tambah objek
            </a>
        </div>

        @include('admin.partials.destinasi-tabs')
        @include('admin.partials.flash')

        <form method="GET" class="mt-6 flex flex-wrap gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama objek"
                   class="w-full max-w-xs rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20">
            <select name="area" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm" aria-label="Filter area">
                <option value="">Semua area</option>
                @foreach ($areas as $a)
                    <option value="{{ $a->id_area }}" @selected(request('area') == $a->id_area)>{{ $a->nama_area }}</option>
                @endforeach
            </select>
            <button class="px-4 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">Terapkan</button>
        </form>

        @if ($objeks->isEmpty())
            <div class="mt-6 bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                <p class="text-slate-700 font-semibold">
                    {{ request('q') || request('area') ? 'Tidak ada objek yang cocok dengan filter itu.' : 'Belum ada objek wisata.' }}
                </p>
                <a href="{{ route('admin.objek.create') }}" class="inline-block mt-3 text-sm font-bold text-brand-orange hover:underline">Tambah objek pertama</a>
            </div>
        @else
            <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($objeks as $o)
                    @php $foto = ($o->foto && file_exists(public_path($o->foto))) ? asset($o->foto) : null; @endphp
                    <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
                        @if ($foto)
                            <img src="{{ $foto }}" alt="Foto {{ $o->nama_objek }}" class="w-full aspect-[4/3] object-cover">
                        @else
                            <div class="w-full aspect-[4/3] bg-slate-100 text-slate-400 flex items-center justify-center text-sm">
                                <i class="fa-regular fa-image mr-2"></i> Belum ada foto
                            </div>
                        @endif

                        <div class="p-4 flex flex-col gap-1.5 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h2 class="font-bold text-slate-900 leading-snug">{{ $o->nama_objek }}</h2>
                                <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-bold
                                    {{ $o->status === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $o->status }}</span>
                            </div>
                            <p class="text-xs text-slate-600">
                                <i class="fa-solid fa-location-dot text-brand-orange"></i>
                                {{ $o->area?->nama_area ?? '-' }}@if($o->area?->kabupaten), {{ $o->area->kabupaten }}@endif
                                &middot; {{ $o->kategori?->nama_kategori ?? 'Tanpa kategori' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                Tiket: {{ $o->harga_tiket !== null ? 'Rp' . number_format($o->harga_tiket, 0, ',', '.') : 'belum diisi' }}
                                &middot; dipakai di {{ $o->paket_count }} paket
                            </p>

                            <div class="mt-auto pt-3 flex flex-wrap gap-2">
                                <a href="{{ route('admin.objek.edit', $o->id_objek) }}"
                                   class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm font-semibold hover:border-brand-blue hover:text-brand-blue">Edit</a>
                                <form method="POST" action="{{ route('admin.objek.toggle', $o->id_objek) }}">
                                    @csrf @method('PATCH')
                                    <button class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">
                                        {{ $o->status === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.objek.destroy', $o->id_objek) }}"
                                      onsubmit="return confirm('Hapus objek wisata ini?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg border border-red-200 text-sm font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">{{ $objeks->links('admin.partials.pagination') }}</div>
        @endif
    </div>
</section>
@endsection