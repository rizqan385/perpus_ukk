<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BorrowingController extends Controller
{
    /**
     * Display the borrowing page with available books.
     */
    public function index(): Response
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            return Inertia::render('Siswa/NeedMembership');
        }

        $books = Book::where('stok', '>', 0)->get()->filter(function ($book) {
            return $book->availableStock() > 0;
        });

        $activeBorrowings = $member->borrowings()
            ->with('book')
            ->where('status', 'dipinjam')
            ->get();

        return Inertia::render('Siswa/Borrow', [
            'books' => $books->values(),
            'activeBorrowings' => $activeBorrowings,
            'member' => $member,
        ]);
    }

    /**
     * Store a new borrowing request.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member || !$member->isActive()) {
            return back()->withErrors(['error' => 'Anda harus menjadi anggota aktif untuk meminjam buku.']);
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if ($book->availableStock() <= 0) {
            return back()->withErrors(['book_id' => 'Maaf, stok buku tidak tersedia.']);
        }

        // Check if member already borrowed this book
        $existingBorrowing = $member->borrowings()
            ->where('book_id', $book->id)
            ->where('status', 'dipinjam')
            ->exists();

        if ($existingBorrowing) {
            return back()->withErrors(['book_id' => 'Anda sudah meminjam buku ini.']);
        }

        // Max 3 books at a time
        $activeBorrowings = $member->borrowings()->where('status', 'dipinjam')->count();
        if ($activeBorrowings >= 3) {
            return back()->withErrors(['error' => 'Anda sudah mencapai batas maksimal peminjaman (3 buku).']);
        }

        Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(7), // 7 days loan period
            'status' => 'dipinjam',
        ]);

        // Kurangi stok buku
        $book->decrement('stok');

        return back()->with('success', 'Buku "' . $book->judul . '" berhasil dipinjam. Harap kembalikan dalam 7 hari.');
    }
}
