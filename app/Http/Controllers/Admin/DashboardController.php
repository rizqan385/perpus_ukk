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
        'pending_borrowings' => Borrowing::where('status', 'menunggu_persetujuan')->count(),
        'active_borrowings'  => Borrowing::where('status', 'menunggu_pengembalian')->count(),
        'total_books'        => Book::count(),
        'total_members'      => Member::count(),
    ];

    $membersByClass = Member::selectRaw("COALESCE(NULLIF(kelas, ''), 'Lainnya') as kelas, COUNT(*) as total")
        ->groupBy('kelas')
        ->orderBy('kelas')
        ->get();

    $days = collect(range(29, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));

    $borrowingsData = Borrowing::selectRaw('DATE(tanggal_pinjam) as date, COUNT(*) as total')
        ->where('tanggal_pinjam', '>=', now()->subDays(30))
        ->groupBy('date')
        ->pluck('total', 'date');

    $returnsData = Borrowing::selectRaw('DATE(tanggal_dikembalikan) as date, COUNT(*) as total')
        ->where('tanggal_dikembalikan', '>=', now()->subDays(30))
        ->whereNotNull('tanggal_dikembalikan')
        ->groupBy('date')
        ->pluck('total', 'date');

    $activityChart = $days->map(fn($date) => [
        'date'    => \Carbon\Carbon::parse($date)->translatedFormat('d M'),
        'borrows' => $borrowingsData->get($date, 0),
        'returns' => $returnsData->get($date, 0),
    ]);

    return Inertia::render('Admin/Dashboard', [
        'stats'          => $stats,
        'membersByClass' => $membersByClass,
        'activityChart'  => $activityChart,
    ]);
}}