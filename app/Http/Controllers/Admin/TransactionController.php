<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the borrowing transactions.
     */
    public function index(Request $request): Response
    {
        $query = Borrowing::with(['member.user', 'book']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('member.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('book', function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%");
                })->orWhereHas('member', function ($q) use ($search) {
                    $q->where('no_anggota', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        $borrowings = $query->latest()->paginate(10);

        return Inertia::render('Admin/Transactions/Index', [
            'borrowings' => $borrowings,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    /**
     * Show the form for creating a new borrowing.
     */
    public function create(): Response
    {
        $members = Member::with('user')
            ->where('status', 'aktif')
            ->get();

        $books = Book::where('stok', '>', 0)->get()->filter(function ($book) {
            return $book->availableStock() > 0;
        });

        return Inertia::render('Admin/Transactions/Create', [
            'members' => $members,
            'books' => $books->values(),
        ]);
    }

    /**
     * Store a newly created borrowing in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        if ($book->availableStock() <= 0) {
            return back()->withErrors(['book_id' => 'Stok buku tidak tersedia.']);
        }

        $member = Member::findOrFail($validated['member_id']);
        if (!$member->isActive()) {
            return back()->withErrors(['member_id' => 'Anggota tidak aktif.']);
        }

        Borrowing::create([
            'member_id' => $validated['member_id'],
            'book_id' => $validated['book_id'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali' => $validated['tanggal_kembali'],
            'status' => 'dipinjam',
        ]);

        // Kurangi stok buku
        $book->decrement('stok');

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi peminjaman berhasil dibuat.');
    }

    /**
     * Display the specified borrowing.
     */
    public function show(Borrowing $transaction): Response
    {
        $transaction->load(['member.user', 'book']);

        return Inertia::render('Admin/Transactions/Show', [
            'borrowing' => $transaction,
        ]);
    }

    /**
     * Return a book.
     */
    public function returnBook(Borrowing $transaction): RedirectResponse
    {
        if ($transaction->status !== 'dipinjam') {
            return back()->withErrors(['error' => 'Buku sudah dikembalikan.']);
        }

        $transaction->returnBook();

        $message = 'Buku berhasil dikembalikan.';
        if ($transaction->denda > 0) {
            $message .= ' Denda: Rp ' . number_format($transaction->denda, 0, ',', '.');
        }

        return redirect()->route('admin.transactions.index')
            ->with('success', $message);
    }

    /**
     * Approve a return request from student.
     */
    public function approveReturn(Borrowing $transaction): RedirectResponse
    {
        if ($transaction->status !== 'menunggu_pengembalian') {
            return back()->withErrors(['error' => 'Request pengembalian tidak valid.']);
        }

        $transaction->returnBook();

        $message = 'Pengembalian buku berhasil disetujui.';
        if ($transaction->denda > 0) {
            $message .= ' Denda keterlambatan: Rp ' . number_format($transaction->denda, 0, ',', '.');
        }

        return redirect()->route('admin.transactions.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified borrowing from storage.
     */
    public function destroy(Borrowing $transaction): RedirectResponse
    {
        $transaction->delete();

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
