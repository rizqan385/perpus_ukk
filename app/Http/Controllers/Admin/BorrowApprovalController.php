<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BorrowApprovalController extends Controller
{
    /**
     * Unified approvals page: pending borrows + pending returns.
     */
    public function index(Request $request): Response
    {
        $search = $request->get('search');

        // ── Pending borrow requests ─────────────────────────────────────
        $borrowQuery = Borrowing::with(['member.user', 'book'])
            ->where('status', 'menunggu_persetujuan')
            ->latest();

        if ($search) {
            $borrowQuery->where(function ($q) use ($search) {
                $q->whereHas('member.user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('member', fn($m) => $m->where('no_anggota', 'like', "%{$search}%"))
                  ->orWhereHas('book', fn($b) => $b->where('judul', 'like', "%{$search}%"));
            });
        }

        $pendingBorrows = $borrowQuery->get()->map(fn(Borrowing $b) => [
            'id'              => $b->id,
            'type'            => 'borrow',
            'tanggal_pinjam'  => $b->tanggal_pinjam,
            'tanggal_kembali' => $b->tanggal_kembali,
            'member' => [
                'id'         => $b->member->id,
                'no_anggota' => $b->member->no_anggota,
                'name'       => $b->member->user->name,
            ],
            'book' => [
                'id'          => $b->book->id,
                'judul'       => $b->book->judul,
                'pengarang'   => $b->book->pengarang,
                'cover_image' => $b->book->cover_image,
            ],
        ]);

        // ── Pending return requests ─────────────────────────────────────
        $returnQuery = Borrowing::with(['member.user', 'book'])
            ->where('status', 'menunggu_pengembalian')
            ->latest();

        if ($search) {
            $returnQuery->where(function ($q) use ($search) {
                $q->whereHas('member.user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('member', fn($m) => $m->where('no_anggota', 'like', "%{$search}%"))
                  ->orWhereHas('book', fn($b) => $b->where('judul', 'like', "%{$search}%"));
            });
        }

        $pendingReturns = $returnQuery->get()->map(fn(Borrowing $b) => [
            'id'              => $b->id,
            'type'            => 'return',
            'tanggal_pinjam'  => $b->tanggal_pinjam,
            'tanggal_kembali' => $b->tanggal_kembali,
            'member' => [
                'id'         => $b->member->id,
                'no_anggota' => $b->member->no_anggota,
                'name'       => $b->member->user->name,
            ],
            'book' => [
                'id'          => $b->book->id,
                'judul'       => $b->book->judul,
                'pengarang'   => $b->book->pengarang,
                'cover_image' => $b->book->cover_image,
            ],
        ]);

        // Real total (unfiltered) for badge count
        $totalBorrows = Borrowing::where('status', 'menunggu_persetujuan')->count();
        $totalReturns = Borrowing::where('status', 'menunggu_pengembalian')->count();

        return Inertia::render('Admin/BorrowApprovals/Index', [
            'pendingBorrows'  => $pendingBorrows,
            'pendingReturns'  => $pendingReturns,
            'totalBorrows'    => $totalBorrows,
            'totalReturns'    => $totalReturns,
            'filters'         => $request->only('search'),
        ]);
    }

    public function approve(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'menunggu_persetujuan') {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses.']);
        }

        if ($borrowing->book->availableStock() <= 0) {
            return back()->withErrors(['error' => 'Stok buku sudah habis.']);
        }

        $borrowing->update([
            'status'          => 'dipinjam',
            'tanggal_pinjam'  => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(7),
        ]);

        $borrowing->book->decrement('stok');
        
        // Kirim notif WA ke anggota
        $borrowing->load('member.user');
        if ($borrowing->member->user->phone) {
            $fonnte = new FonnteService();
            $fonnte->send($borrowing->member->user->phone,
                "Halo *{$borrowing->member->user->name}*! 📚\n\n"
                . "Permintaan pinjam buku disetujui.\n"
                . "Judul: *{$borrowing->book->judul}*\n"
                . "Tgl pinjam: " . Carbon::parse($borrowing->tanggal_pinjam)->format('d/m/Y') . "\n"
                . "Batas kembali: " . Carbon::parse($borrowing->tanggal_kembali)->format('d/m/Y') . "\n\n"
                . "Mohon kembalikan tepat waktu. Selamat membaca!"
            );
        }

        return back()->with('success', "Permintaan pinjam \"{$borrowing->book->judul}\" dari {$borrowing->member->user->name} disetujui.");
    }

    public function reject(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'menunggu_persetujuan') {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses.']);
        }

        $bookTitle  = $borrowing->book->judul;
        $memberName = $borrowing->member->user->name;

        $borrowing->delete();
        
        // Kirim notif WA ke anggota
        if ($borrowing->member->user->phone) {
            $fonnte = new FonnteService();
            $fonnte->send($borrowing->member->user->phone,
                "Halo *{$memberName}*! 📚\n\n"
                . "Mohon maaf, permintaan pinjam buku ditolak.\n"
                . "Judul: *{$bookTitle}*\n\n"
                . "Silakan hubungi admin perpustakaan untuk informasi lebih lanjut."
            );
        }

        return back()->with('success', "Permintaan pinjam \"{$bookTitle}\" dari {$memberName} ditolak.");
    }

    /**
     * Approve a return request (moved here from TransactionController).
     */
    public function approveReturn(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'menunggu_pengembalian') {
            return back()->withErrors(['error' => 'Request pengembalian tidak valid.']);
        }

        $borrowing->returnBook();

        $message = 'Pengembalian buku berhasil disetujui.';
        if ($borrowing->denda > 0) {
            $message .= ' Denda keterlambatan: Rp ' . number_format($borrowing->denda, 0, ',', '.');
        }

        // Kirim notif WA ke anggota
        $borrowing->load('member.user', 'book');
        if ($borrowing->member->user->phone) {
            $fonnte = new FonnteService();
            $waMessage = "Halo *{$borrowing->member->user->name}*!\n\n"
                . "Pengembalian buku telah dikonfirmasi oleh Admin.\n"
                . "Judul: *{$borrowing->book->judul}*\n";

            if ($borrowing->denda > 0) {
                $waMessage .= "Denda: *Rp " . number_format($borrowing->denda, 0, ',', '.') . "*\n"
                    . "Harap segera lunasi denda di perpustakaan.\n";
            }

            $waMessage .= "\nTerima kasih!";
            $fonnte->send($borrowing->member->user->phone, $waMessage);
        }

        return back()->with('success', $message);
    }
}
