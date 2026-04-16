<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Services\FonnteService;
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
            return redirect()->route('siswa.register')
                ->with('info', 'Lengkapi data diri terlebih dahulu untuk meminjam buku.');
        }

        // Block if has unpaid fines
        $unpaidFinesAmount = $member->borrowings()->where('denda', '>', 0)->whereNull('payment_status')->sum('denda');
        if ($unpaidFinesAmount > 0) {
            return redirect()->route('siswa.fines')->with('error', 'Anda memiliki denda sebesar Rp ' . number_format($unpaidFinesAmount, 0, ',', '.') . ' yang belum dilunasi. Harap bayar denda terlebih dahulu.');
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

        // 2. CEK LIMIT PINJAM (Misal: Max 3 buku sekaligus)
        $activeBorrowCount = $member->borrowings()->whereIn('status', ['dipinjam', 'menunggu_persetujuan'])->count();
        if ($activeBorrowCount >= 3) {
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

        // Kirim notif WA ke siswa
        if ($user->phone) {
            $fonnte = new FonnteService();
            $fonnte->send($user->phone,
                "Halo *{$user->name}*! 📚\n\n"
                . "Permintaan peminjaman buku berhasil dikirim.\n"
                . "Judul: *{$book->judul}*\n\n"
                . "Permintaan sedang menunggu persetujuan Admin.\n"
                . "Kami akan memberitahu kamu saat disetujui. 🙏"
            );
        }

        return redirect()->route('siswa.card')
            ->with('success', 'Permintaan pinjam buku "' . $book->judul . '" berhasil dikirim. Menunggu konfirmasi admin.');
    }
}
