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
            ->whereIn('status', ['dipinjam', 'menunggu_persetujuan'])
            ->get();

        $unpaidFines = $member->borrowings()
            ->where('denda', '>', 0)
            ->whereNull('payment_status')
            ->sum('denda');

        return Inertia::render('Siswa/Borrow', [
            'books'           => $books->values(),
            'activeBorrowings' => $activeBorrowings,
            'member'          => $member,
            'unpaidFines'     => $unpaidFines,
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

        // Block if has unpaid fines
        $unpaidFines = $member->borrowings()->where('denda', '>', 0)->whereNull('payment_status')->sum('denda');
        if ($unpaidFines > 0) {
            return back()->withErrors(['error' => 'Anda masih memiliki denda belum lunas sebesar Rp ' . number_format($unpaidFines, 0, ',', '.') . '. Harap bayar denda terlebih dahulu sebelum meminjam buku.']);
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if ($book->availableStock() <= 0) {
            return back()->withErrors(['book_id' => 'Maaf, stok buku tidak tersedia.']);
        }

        // Check if member already borrowed OR has pending request for this book
        $existingBorrowing = $member->borrowings()
            ->where('book_id', $book->id)
            ->whereIn('status', ['dipinjam', 'menunggu_persetujuan'])
            ->exists();

        if ($existingBorrowing) {
            return back()->withErrors(['book_id' => 'Anda sudah meminjam atau memiliki permintaan aktif untuk buku ini.']);
        }

        // Max 3 books at a time (including pending)
        $activeBorrowings = $member->borrowings()
            ->whereIn('status', ['dipinjam', 'menunggu_persetujuan'])
            ->count();
        if ($activeBorrowings >= 3) {
            return back()->withErrors(['error' => 'Anda sudah mencapai batas maksimal peminjaman (3 buku).']);
        }

        // Create borrowing request — admin must approve before status becomes 'dipinjam'
        Borrowing::create([
            'member_id'      => $member->id,
            'book_id'        => $book->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(7),
            'status'         => 'menunggu_persetujuan',
        ]);

        // Stock is NOT decremented yet — decremented only when admin approves

        return back()->with('success', 'Permintaan pinjam buku "' . $book->judul . '" berhasil dikirim. Menunggu konfirmasi admin.');
    }
}
