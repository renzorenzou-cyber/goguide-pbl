<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tour->title }} - Go Go Guide</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

<div class="max-w-5xl mx-auto px-4 py-10">

    <a href="{{ route('home') }}" class="text-sm text-blue-900 hover:underline">&larr; Kembali</a>

    <div class="mt-4 grid gap-8 md:grid-cols-3">

        {{-- Kiri: info tour --}}
        <div class="md:col-span-2">
            <div class="h-64 bg-gray-300 rounded-xl overflow-hidden">
                @if($tour->image)
                    <img src="{{ asset('storage/'.$tour->image) }}" class="w-full h-full object-cover">
                @endif
            </div>

            <p class="text-orange-500 text-xs font-semibold uppercase mt-6">{{ $tour->region->name }}</p>
            <h1 class="text-3xl font-bold mt-1">{{ $tour->title }}</h1>

            <div class="flex gap-4 text-sm text-gray-500 mt-2">
                <span>⭐ {{ $tour->rating }} ({{ $tour->review_count }} review)</span>
                <span>⏱ {{ $tour->duration }}</span>
            </div>

            <h2 class="font-bold text-lg mt-8">Tentang tour ini</h2>
            <p class="text-gray-600 mt-2">{{ $tour->description }}</p>

            <h2 class="font-bold text-lg mt-8">Guide kamu</h2>
            <div class="bg-white rounded-xl shadow p-4 mt-2">
                <p class="font-semibold">{{ $tour->guide->user->name }}</p>
                <p class="text-sm text-blue-900">{{ $tour->guide->specialty }}</p>
                <p class="text-sm text-gray-500 mt-1">Bahasa: {{ $tour->guide->languages }}</p>
                <p class="text-sm text-gray-600 mt-2">{{ $tour->guide->bio }}</p>
            </div>
        </div>

        {{-- Kanan: kartu harga --}}
        <div>
            <div class="bg-white rounded-xl shadow p-5 sticky top-6"
     x-data="{ price: {{ $tour->price }}, qty: {{ old('participants', 1) }} }">

    <span class="text-xs text-gray-400">Mulai dari</span>
    <p class="text-2xl font-bold">Rp {{ number_format($tour->price, 0, ',', '.') }}</p>
    <p class="text-xs text-gray-400">per orang</p>

    @auth
        <form method="POST" action="{{ route('bookings.store', $tour->slug) }}" class="mt-4 space-y-3">
            @csrf

            <div>
                <label class="text-sm font-medium">Tanggal tour</label>
                <input type="date" name="tour_date"
                       value="{{ old('tour_date') }}"
                       min="{{ now()->toDateString() }}"
                       class="w-full border rounded px-3 py-2 text-sm mt-1" required>
                @error('tour_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Jumlah orang</label>
                <input type="number" name="participants" x-model.number="qty"
                       min="1" max="20"
                       class="w-full border rounded px-3 py-2 text-sm mt-1" required>
                @error('participants')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Catatan (opsional)</label>
                <textarea name="notes" rows="2"
                          class="w-full border rounded px-3 py-2 text-sm mt-1">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-between border-t pt-3">
                <span class="text-sm text-gray-500">Total</span>
                <span class="font-bold" x-text="'Rp ' + (price * (qty || 0)).toLocaleString('id-ID')"></span>
            </div>

            <button type="submit" class="w-full bg-blue-900 text-white py-2 rounded hover:bg-blue-800">
                Book Sekarang
            </button>
        </form>
    @else
        <a href="{{ route('login') }}"
           class="block text-center w-full mt-4 bg-blue-900 text-white py-2 rounded hover:bg-blue-800">
            Login untuk Booking
        </a>
        <p class="text-xs text-gray-400 mt-2 text-center">
            Belum punya akun? <a href="{{ route('register') }}" class="underline">Daftar</a>
        </p>
    @endauth
            </div>
        </div>

    </div>
</div>

</body>
</html>