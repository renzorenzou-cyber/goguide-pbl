@if ($paginator->hasPages())
    @php
        $btn  = 'inline-flex items-center justify-center min-w-[2.25rem] h-9 px-3 rounded-lg border text-sm font-semibold transition';
        $off  = 'border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed';
        $on   = 'border-slate-200 bg-white text-slate-600 hover:border-brand-orange hover:text-brand-orange';
        $curr = 'border-brand-blue bg-brand-blue text-white';
    @endphp

    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">
            Menampilkan <span class="font-bold text-slate-700">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</span>
            dari <span class="font-bold text-slate-700">{{ $paginator->total() }}</span> data
        </p>

        <div class="flex flex-wrap items-center gap-1.5">
            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="{{ $btn }} {{ $off }}" aria-disabled="true"><i class="fa-solid fa-chevron-left text-xs"></i><span class="sr-only">Sebelumnya</span></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $btn }} {{ $on }}"><i class="fa-solid fa-chevron-left text-xs"></i><span class="sr-only">Sebelumnya</span></a>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-slate-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="{{ $btn }} {{ $curr }}">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="{{ $btn }} {{ $on }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $btn }} {{ $on }}"><i class="fa-solid fa-chevron-right text-xs"></i><span class="sr-only">Berikutnya</span></a>
            @else
                <span class="{{ $btn }} {{ $off }}" aria-disabled="true"><i class="fa-solid fa-chevron-right text-xs"></i><span class="sr-only">Berikutnya</span></span>
            @endif
        </div>
    </nav>
@endif