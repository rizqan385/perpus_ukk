<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): Response
    {
        $stats = [
            'total_books' => Book::count(),
            'total_members' => Member::count(),
            'active_borrowings' => Borrowing::where('status', 'dipinjam')->count(),
            'overdue_borrowings' => Borrowing::where('status', 'dipinjam')
                ->where('tanggal_kembali', '<', now())
                ->count(),
        ];

        $recentBorrowings = Borrowing::with(['member.user', 'book'])
            ->latest()
            ->take(5)
            ->get();

        $popularBooks = Book::withCount(['borrowings' => function ($query) {
            $query->where('created_at', '>=', now()->subMonth());
        }])
            ->orderByDesc('borrowings_count')
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentBorrowings' => $recentBorrowings,
            'popularBooks' => $popularBooks,
        ]);
    }
}
