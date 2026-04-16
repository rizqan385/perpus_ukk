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
            'total_books'        => Book::count(),
            'total_members'      => Member::count(),
            'active_borrowings'  => Borrowing::where('status', 'dipinjam')->count(),
            'overdue_borrowings' => Borrowing::where('status', 'dipinjam')
                ->where('tanggal_kembali', '<', now())
                ->count(),
            'total_borrowings'   => Borrowing::count(),
        ];

        // Anggota per kelas (null/kosong dikelompokkan sebagai "Lainnya")
        $membersByClass = Member::selectRaw("COALESCE(NULLIF(kelas, ''), 'Lainnya') as kelas, COUNT(*) as total")
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get()
            ->map(fn($row) => [
                'kelas' => $row->kelas,
                'total' => $row->total,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats'          => $stats,
            'membersByClass' => $membersByClass,
        ]);
    }
}

