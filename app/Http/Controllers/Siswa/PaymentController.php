<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Get Snap Token for a specific borrowing fine.
     */
    public function getSnapToken(Borrowing $borrowing)
    {
        // 1. Validasi: Pastikan punya denda dan belum bayar
        if ($borrowing->denda <= 0) {
            return response()->json(['error' => 'Tidak ada denda pada peminjaman ini.'], 400);
        }

        if ($borrowing->payment_status === 'paid') {
            return response()->json(['error' => 'Denda sudah lunas.'], 400);
        }

        // 2. Siapkan parameter Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => 'FINE-' . $borrowing->id . '-' . time(),
                'gross_amount' => (int) $borrowing->denda,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone,
            ],
            'item_details' => [
                [
                    'id' => 'Denda-' . $borrowing->id,
                    'price' => (int) $borrowing->denda,
                    'quantity' => 1,
                    'name' => 'Denda Buku: ' . $borrowing->book->judul,
                ]
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle Midtrans Notification (Webhook).
     */
    public function handleNotification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        $orderIdParts = explode('-', $notification->order_id);
        $borrowingId = $orderIdParts[1];

        $borrowing = Borrowing::find($borrowingId);

        if (!$borrowing) {
            return response()->json(['message' => 'Invalid Order'], 404);
        }

        $transactionStatus = $notification->transaction_status;
        $type = $notification->payment_type;

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($notification->fraud_status == 'accept') {
                    $borrowing->update(['payment_status' => 'paid']);
                }
            }
        } else if ($transactionStatus == 'settlement') {
            $borrowing->update(['payment_status' => 'paid']);
        } else if ($transactionStatus == 'pending') {
            $borrowing->update(['payment_status' => 'pending']);
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $borrowing->update(['payment_status' => 'failed']);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Mark as paid directly (For Demo/Localhost only).
     */
    public function successPayment(Borrowing $borrowing)
    {
        $borrowing->update(['payment_status' => 'paid']);
        return redirect()->route('siswa.fines')->with('success', 'Pembayaran berhasil dikonfirmasi!');
    }
}
