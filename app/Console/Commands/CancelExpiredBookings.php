<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';

    protected $description = 'Batalkan booking belum_bayar yang melewati batas bayar';

    public function handle(): int
    {
        $jumlah = Booking::where('status', 'belum_bayar')
            ->whereNotNull('batas_bayar')
            ->where('batas_bayar', '<', now())
            ->update([
                'status' => 'dibatalkan',
                'alasan_batal' => 'Batas waktu pembayaran terlewati',
            ]);

        $this->info($jumlah . ' booking dibatalkan.');

        return self::SUCCESS;
    }
}
