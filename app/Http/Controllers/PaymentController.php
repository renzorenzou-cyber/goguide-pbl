<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function pay(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Booking ini tidak bisa dibayar.'], 422);
        }

        // Kalau sebelumnya popup ditutup, pakai token yang sama
        $existing = $booking->payments()
            ->where('status', 'pending')
            ->whereNotNull('snap_token')
            ->latest()
            ->first();

        if ($existing) {
            return response()->json(['token' => $existing->snap_token]);
        }

        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $booking->load('tour');
        $user    = $request->user();
        $orderId = $booking->booking_code . '-' . now()->timestamp;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
            ],
            'item_details' => [[
                'id'       => 'tour-' . $booking->tour->id,
                'price'    => (int) $booking->tour->price,
                'quantity' => (int) $booking->participants,
                'name'     => Str::limit($booking->tour->title, 50, ''),
            ]],
        ];

        try {
            $token = Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Gagal menghubungi Midtrans: ' . $e->getMessage()], 500);
        }

        $booking->payments()->create([
            'order_id'   => $orderId,
            'snap_token' => $token,
            'status'     => 'pending',
        ]);

        return response()->json(['token' => $token]);
    }

    public function simulate(Request $request, Booking $booking)
{
    abort_unless(app()->environment('local'), 404);
    abort_unless($booking->user_id === $request->user()->id, 403);
    abort_unless($booking->status === 'pending', 422);

    $booking->payments()->create([
        'order_id'     => $booking->booking_code . '-SIM-' . now()->timestamp,
        'payment_type' => 'simulasi',
        'status'       => 'paid',
        'paid_at'      => now(),
    ]);
    $booking->update(['status' => 'paid']);

    return redirect()->route('bookings.show', $booking);
}
}