<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReturnController extends Controller
{
    /**
     * Display the return page with borrowed books.
     */
    public function index(): Response
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            return Inertia::render('Siswa/NeedMembership');
        }

        $activeBorrowings = $member->borrowings()
            ->with('book')
            ->where('status', 'dipinjam')
            ->get();

        $returnHistory = $member->borrowings()
            ->with('book')
            ->whereIn('status', ['dikembalikan', 'terlambat'])
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Siswa/Returns', [
            'activeBorrowings' => $activeBorrowings,
            'returnHistory' => $returnHistory,
            'member' => $member,
        ]);
    }

    /**
     * Return a borrowed book.
     */
    public function returnBook(Borrowing $borrowing): RedirectResponse
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member || $borrowing->member_id !== $member->id) {
            return back()->withErrors(['error' => 'Anda tidak berhak mengembalikan buku ini.']);
        }

        if ($borrowing->status !== 'dipinjam') {
            return back()->withErrors(['error' => 'Buku ini sudah dikembalikan.']);
        }

        $borrowing->returnBook();

        $message = 'Buku "' . $borrowing->book->judul . '" berhasil dikembalikan.';
        if ($borrowing->denda > 0) {
            $message .= ' Denda keterlambatan: Rp ' . number_format($borrowing->denda, 0, ',', '.');
        }

        return back()->with('success', $message);
    }
}
