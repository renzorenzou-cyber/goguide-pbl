<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Saya - Go Go Guide</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

<div class="max-w-3xl mx-auto px-4 py-10">

    <a href="{{ route('home') }}" class="text-sm text-blue-900 hover:underline">&larr; Kembali ke beranda</a>
    <h1 class="text-2xl font-bold mt-4 mb-6">Booking Saya</h1>

    @php
        $badges = [
            'pending'   => 'bg-yellow-100 text-yellow-700',
            'paid'      => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="space-y-4">
        @forelse($bookings as $booking)
            <a href="{{ route('bookings.show', $booking) }}"
               class="block bg-white rounded-xl shadow p-5 hover:shadow-md transition">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs text-gray-400">{{ $booking->booking_code }}</p>
                        <h2 class="font-bold mt-1">{{ $booking->tour->title }}</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $booking->tour->region->name }}
                            &middot; {{ $booking->tour_date->format('d M Y') }}
                            &middot; {{ $booking->participants }} orang
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded text-xs font-semibold uppercase {{ $badges[$booking->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $booking->status }}
                    </span>
                </div>
                <p class="font-bold mt-3">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
            </a>
        @empty
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                Kamu belum punya booking.
                <a href="{{ route('home') }}" class="text-blue-900 underline">Cari tour</a>
            </div>
        @endforelse
    </div>
</div>

</body>
</html>