@php
    $tabs = [
        ['admin.paket.*',    'admin.paket.index',    'fa-box-open',     'Paket'],
        ['admin.objek.*',    'admin.objek.index',    'fa-mountain-sun', 'Objek wisata'],
        ['admin.area.*',     'admin.area.index',     'fa-location-dot', 'Area'],
        ['admin.kategori.*', 'admin.kategori.index', 'fa-tags',         'Kategori'],
    ];
@endphp
<nav aria-label="Kelola destinasi" class="mt-4 flex flex-wrap gap-2">
    @foreach ($tabs as [$pola, $rute, $ikon, $label])
        <a href="{{ route($rute) }}"
           @if(request()->routeIs($pola)) aria-current="page" @endif
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition
                  {{ request()->routeIs($pola) ? 'bg-brand-blue text-white border-brand-blue' : 'bg-white text-slate-600 border-slate-200 hover:border-brand-orange hover:text-brand-orange' }}">
            <i class="fa-solid {{ $ikon }}"></i> {{ $label }}
        </a>
    @endforeach
</nav>