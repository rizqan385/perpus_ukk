<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FineController extends Controller
{
    /**
     * Display all fines.
     */
    public function index(Request $request): Response
    {
        $query = Borrowing::with(['member.user', 'book'])
            ->where('denda', '>', 0);

        if ($request->get('status') === 'pending') {
            $query->where('payment_status', 'pending');
        } elseif ($request->get('status') === 'unpaid') {
            $query->whereNull('payment_status');
        } elseif ($request->get('status') === 'paid') {
            $query->where('payment_status', 'paid');
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('member.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('member', function ($q) use ($search) {
                $q->where('no_anggota', 'like', "%{$search}%");
            });
        }

        $fines = $query->latest()->paginate(10);

        $stats = [
            'total_unpaid' => Borrowing::where('denda', '>', 0)->whereNull('payment_status')->sum('denda'),
            'total_pending' => Borrowing::where('denda', '>', 0)->where('payment_status', 'pending')->sum('denda'),
            'total_paid' => Borrowing::where('denda', '>', 0)->where('payment_status', 'paid')->sum('denda'),
        ];

        return Inertia::render('Admin/Fines/Index', [
            'fines' => $fines,
            'stats' => $stats,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    /**
     * Confirm a fine payment.
     */
    public function confirm(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->denda <= 0) {
            return back()->withErrors(['error' => 'Tidak ada denda untuk dikonfirmasi.']);
        }

        $borrowing->update([
            'payment_status' => 'paid',
            'denda' => 0, // Clear the fine after payment
        ]);

        return back()->with('success', 'Pembayaran denda berhasil dikonfirmasi.');
    }
}
