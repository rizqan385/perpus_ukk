<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Services\FonnteService;
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

        $pendingReturns = $member->borrowings()
            ->with('book')
            ->where('status', 'menunggu_pengembalian')
            ->get();

        $returnHistory = $member->borrowings()
            ->with('book')
            ->whereIn('status', ['dikembalikan', 'terlambat'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Siswa/Returns', [
            'activeBorrowings' => $activeBorrowings,
            'pendingReturns' => $pendingReturns,
            'returnHistory' => $returnHistory,
            'member' => $member,
        ]);
    }

    /**
     * Request return for a borrowed book.
     */
    public function requestReturn(Borrowing $borrowing): RedirectResponse
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member || $borrowing->member_id !== $member->id) {
            return back()->withErrors(['error' => 'Anda tidak berhak melakukan request pengembalian untuk buku ini.']);
        }

        if ($borrowing->status !== 'dipinjam') {
            return back()->withErrors(['error' => 'Buku ini tidak dapat di-request pengembalian.']);
        }

        $borrowing->requestReturn();

        // Kirim notif WA ke siswa
        $phoneNumber = $member->telepon ?? $user->phone;
        
        if ($phoneNumber) {
            $fonnte = new FonnteService();
            $fonnte->send($phoneNumber,
                "Halo *{$user->name}*! 📚\n\n"
                . "Permintaan pengembalian buku berhasil dikirim.\n"
                . "Judul: *{$borrowing->book->judul}*\n\n"
                . "Permintaan sedang menunggu konfirmasi Admin.\n"
                . "Harap segera kembalikan buku ke perpustakaan. 🙏"
            );
        }

        return back()->with('success', 'Request pengembalian buku "' . $borrowing->book->judul . '" berhasil dikirim. Menunggu persetujuan admin.');
    }
}
