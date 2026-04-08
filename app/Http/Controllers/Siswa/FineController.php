<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FineController extends Controller
{
    /**
     * Display the student's fines.
     */
    public function index(): Response
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            return Inertia::render('Siswa/NeedMembership');
        }

        $activeFines = $member->borrowings()
            ->with('book')
            ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
            ->whereDate('tanggal_kembali', '<', now())
            ->get();

        $unpaidFines = $member->borrowings()
            ->with('book')
            ->where('denda', '>', 0)
            ->whereNull('payment_status')
            ->get();

        $pendingFines = $member->borrowings()
            ->with('book')
            ->where('denda', '>', 0)
            ->where('payment_status', 'pending')
            ->get();

        $paidFines = $member->borrowings()
            ->with('book')
            ->where('denda', '>', 0)
            ->where('payment_status', 'paid')
            ->latest()
            ->take(10)
            ->get();

        $activeFinesTotal = $activeFines->sum(function(Borrowing $b) { return $b->calculateFine(); });
        $totalUnpaid = $unpaidFines->sum('denda') + $activeFinesTotal;
        $totalPending = $pendingFines->sum('denda');

        return Inertia::render('Siswa/Fines', [
            'activeFines' => $activeFines,
            'unpaidFines' => $unpaidFines,
            'pendingFines' => $pendingFines,
            'paidFines' => $paidFines,
            'totalUnpaid' => $totalUnpaid,
            'totalPending' => $totalPending,
            'member' => $member,
        ]);
    }

    /**
     * Mark a fine as paid (pending confirmation).
     */
    public function pay(Borrowing $borrowing): RedirectResponse
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member || $borrowing->member_id !== $member->id) {
            return back()->withErrors(['error' => 'Anda tidak berhak membayar denda ini.']);
        }

        if ($borrowing->denda <= 0) {
            return back()->withErrors(['error' => 'Tidak ada denda yang harus dibayar.']);
        }

        if ($borrowing->payment_status !== null) {
            return back()->withErrors(['error' => 'Denda ini sudah dalam proses pembayaran.']);
        }

        $borrowing->update(['payment_status' => 'pending']);

        return back()->with('success', 'Pembayaran denda berhasil diajukan. Menunggu konfirmasi admin.');
    }
}
