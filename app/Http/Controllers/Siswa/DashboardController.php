<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index(): Response
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            return Inertia::render('Siswa/Dashboard', [
                'hasMembership' => false,
                'stats' => null,
                'activeBorrowings' => [],
                'borrowingHistory' => [],
            ]);
        }

        $activeFinesTotal = $member->borrowings()
            ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
            ->get()
            ->filter(function(Borrowing $b) { return $b->isOverdue(); })
            ->sum(function(Borrowing $b) { return $b->calculateFine(); });

        $unpaidFinesTotal = $member->borrowings()
            ->where('denda', '>', 0)
            ->whereNull('payment_status')
            ->sum('denda');

        $stats = [
            'active_borrowings' => $member->borrowings()->where('status', 'dipinjam')->count(),
            'total_borrowed' => $member->borrowings()->count(),
            'total_fines' => $unpaidFinesTotal + $activeFinesTotal,
        ];

        $activeBorrowings = $member->borrowings()
            ->with('book')
            ->where('status', 'dipinjam')
            ->get();

        $borrowingHistory = $member->borrowings()
            ->with('book')
            ->whereIn('status', ['dikembalikan', 'terlambat'])
            ->latest()
            ->take(5)
            ->get();

        $unpaidFines = $member->borrowings()
            ->with('book')
            ->where('denda', '>', 0)
            ->whereNull('payment_status')
            ->get();

        return Inertia::render('Siswa/Dashboard', [
            'hasMembership' => true,
            'member' => $member,
            'stats' => $stats,
            'activeBorrowings' => $activeBorrowings,
            'borrowingHistory' => $borrowingHistory,
            'unpaidFines' => $unpaidFines,
        ]);
    }
}
