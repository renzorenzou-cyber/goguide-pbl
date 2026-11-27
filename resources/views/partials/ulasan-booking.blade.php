{{-- Rating dan ulasan untuk booking yang sudah selesai (dipakai di bookings/show). --}}
@php $ulasan = $booking->ulasan; @endphp
<div class="mt-4 bg-white rounded-2xl border border-slate-200 p-5">
    @if ($ulasan)
        <h3 class="font-bold text-slate-900">Ulasanmu untuk {{ $booking->guide?->nama_guide }}</h3>
        <div class="mt-2">@include('partials.bintang', ['nilai' => $ulasan->rating])</div>
        @if ($ulasan->komentar)
            <p class="mt-2 text-sm text-slate-600">{!! nl2br(e($ulasan->komentar)) !!}</p>
        @endif
        <p class="mt-2 text-xs text-slate-400">Dikirim {{ $ulasan->created_at?->format('d M Y') }}. Ulasan tidak bisa diubah.</p>
    @else
        <h3 class="font-bold text-slate-900">Beri rating untuk {{ $booking->guide?->nama_guide }}</h3>
        <p class="text-sm text-slate-500 mt-1">Ceritamu membantu wisatawan lain memilih guide.</p>
        <div class="mt-4">@include('partials.form-ulasan')</div>
    @endif
</div>