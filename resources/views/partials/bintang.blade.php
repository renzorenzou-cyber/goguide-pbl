{{-- Tampilan bintang read-only. Pakai: @include('partials.bintang', ['nilai' => 4]) --}}
@php $bintang = max(0, min(5, (int) round($nilai ?? 0))); @endphp
<span class="inline-flex leading-none text-lg" role="img" aria-label="{{ $bintang }} dari 5 bintang">
    @for ($i = 1; $i <= 5; $i++)<span class="{{ $i <= $bintang ? 'text-amber-400' : 'text-slate-300' }}" aria-hidden="true">&#9733;</span>@endfor
</span>