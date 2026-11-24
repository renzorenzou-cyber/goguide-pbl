@extends('layouts.site')

@section('title', 'Notifikasi - GoGuide')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-blue">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
        </a>

        <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Notifikasi</h1>
                <p class="text-sm text-slate-500 mt-1">
                    @if ($belumDibaca > 0) {{ $belumDibaca }} belum dibaca. @else Semua sudah dibaca. @endif
                </p>
            </div>
            @if ($belumDibaca > 0)
                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                    @csrf
                    <button class="px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold hover:border-brand-orange hover:text-brand-orange">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        @include('admin.partials.flash')

        @if ($notifikasi->isEmpty())
            <div class="mt-6 bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                <p class="font-semibold text-slate-700">Belum ada notifikasi.</p>
            </div>
        @else
            <ul class="mt-6 space-y-3">
                @foreach ($notifikasi as $n)
                    <li>
                        <form method="POST" action="{{ route('notifikasi.baca', $n->id_notifikasi) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left rounded-2xl border p-4 transition hover:border-brand-orange hover:shadow-md
                                           {{ $n->dibaca ? 'bg-white border-slate-200' : 'bg-orange-50/60 border-orange-200' }}">
                                <div class="flex items-start gap-3">
                                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $n->dibaca ? 'bg-transparent' : 'bg-brand-orange' }}"></span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center justify-between gap-x-3">
                                            <p class="font-bold text-slate-900 {{ $n->dibaca ? 'font-semibold' : '' }}">{{ $n->judul }}</p>
                                            <p class="text-xs text-slate-500">{{ $n->created_at?->translatedFormat('d M Y H:i') }}</p>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-600">{{ $n->isi }}</p>
                                        @if ($n->booking)
                                            <p class="mt-1 text-xs font-mono text-slate-500">{{ $n->booking->kode_booking }}</p>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>

            <div class="mt-8">{{ $notifikasi->links('admin.partials.pagination') }}</div>
        @endif
    </div>
</section>
@endsection