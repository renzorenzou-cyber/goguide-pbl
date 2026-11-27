{{--
    Form rating guide (bintang + komentar). Dipakai di halaman detail booking dan halaman "Ulasan Saya".
    Variabel: $booking (wajib), $dari (opsional, 'ulasan' = setelah kirim kembali ke daftar Ulasan Saya)
--}}
<form method="POST" action="{{ route('bookings.ulasan', $booking->id_booking) }}">
    @csrf
    @if (($dari ?? null) === 'ulasan')
        <input type="hidden" name="dari" value="ulasan">
    @endif

    {{-- Bintang: urutan DOM 5 ke 1 dengan flex-row-reverse supaya tampil 1 ke 5 dan bintang di kiri ikut menyala --}}
    <fieldset>
        <legend class="text-sm font-semibold text-slate-700">Rating</legend>
        <div class="mt-1 flex flex-row-reverse justify-end">
            @foreach ([5, 4, 3, 2, 1] as $n)
                <input id="bintang{{ $booking->id_booking }}_{{ $n }}" type="radio" name="rating" value="{{ $n }}" class="peer sr-only" @checked((int) old('rating') === $n) required>
                <label for="bintang{{ $booking->id_booking }}_{{ $n }}" title="{{ $n }} bintang"
                       class="cursor-pointer px-0.5 text-4xl leading-none text-slate-300 hover:text-amber-300 peer-checked:text-amber-400">
                    <span class="sr-only">{{ $n }} bintang</span><span aria-hidden="true">&#9733;</span>
                </label>
            @endforeach
        </div>
        @error('rating')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
    </fieldset>

    <div class="mt-4">
        <label for="komentar{{ $booking->id_booking }}" class="text-sm font-semibold text-slate-700">Komentar (opsional)</label>
        <textarea id="komentar{{ $booking->id_booking }}" name="komentar" rows="3" maxlength="500"
                  class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20"
                  placeholder="Bagaimana pengalamanmu bersama guide ini?">{{ old('komentar') }}</textarea>
        @error('komentar')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
    </div>

    <button class="mt-4 px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white text-sm font-bold shadow">Kirim ulasan</button>
</form>