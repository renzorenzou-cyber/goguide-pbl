@php
    $namaArea = $p->objek->map(fn ($o) => $o->area?->nama_area)->filter()->unique()->implode(', ');
@endphp
<div class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 hover:shadow-xl transition-all flex flex-col">
    <div class="h-48">
        @if($p->foto_url)
            <img src="{{ $p->foto_url }}" alt="{{ $p->nama_paket }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-sky-300 via-sky-200 to-emerald-200 flex items-center justify-center">
                <i class="fa-solid fa-mountain-sun text-5xl text-white/80"></i>
            </div>
        @endif
    </div>
    <div class="p-5 flex-1 flex flex-col justify-between">
        <div>
            <span class="text-xs font-bold text-brand-orange uppercase">{{ $namaArea }}</span>
            <h3 class="text-lg font-bold text-slate-900 mt-0.5">{{ $p->nama_paket }}</h3>
            @php
    $daftarObjek = $p->objek->pluck('nama_objek');
    $objekTampil = $daftarObjek->take(6);
    $objekSisa   = $daftarObjek->count() - $objekTampil->count();
@endphp
@if ($daftarObjek->isNotEmpty())
    <div class="mt-3 flex flex-wrap gap-1.5">
        @foreach ($objekTampil as $namaObjek)
            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-semibold">{{ $namaObjek }}</span>
        @endforeach
        @if ($objekSisa > 0)
            <span class="px-2.5 py-1 rounded-full bg-slate-200 text-slate-600 text-[11px] font-semibold">+{{ $objekSisa }} lagi</span>
        @endif
    </div>
@endif
            <p class="text-xs text-slate-400 mt-1">
                {{ $p->durasi }} &middot; {{ $p->jumlah_guide }} guide tersedia
            </p>
        </div>
        <div class="mt-5 pt-4 border-t border-slate-200/60 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400">Mulai dari</span>
                <p class="text-lg font-extrabold text-brand-blue">
                    Rp {{ number_format($p->harga_paket, 0, ',', '.') }}
                    <span class="text-xs font-normal text-slate-500">/ peserta</span>
                </p>
            </div>
            <a href="{{ route('paket.show', $p->id_paket) }}" class="px-4 py-2 bg-brand-blue/10 hover:bg-brand-blue hover:text-white text-brand-blue font-bold text-xs rounded-xl transition-colors">
                Lihat Paket
            </a>
        </div>
    </div>
</div>