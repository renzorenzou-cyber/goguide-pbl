<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired {--minutes=30}';

    protected $description = 'Batalkan booking pending yang tidak dibayar dalam batas waktu';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');

        $expired = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($minutes))
            ->get();

        foreach ($expired as $booking) {
            $booking->update(['status' => 'cancelled']);
            $booking->payments()
                ->where('status', 'pending')
                ->update(['status' => 'expired']);
        }

        $this->info($expired->count() . ' booking dibatalkan.');

        return self::SUCCESS;
    }
}