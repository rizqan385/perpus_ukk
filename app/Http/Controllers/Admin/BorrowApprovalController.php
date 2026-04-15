<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BorrowApprovalController extends Controller
{
    public function index(): Response
    {
        $pending = Borrowing::with(['member.user', 'book'])
            ->where('status', 'menunggu_persetujuan')
            ->latest()
            ->get()
            ->map(fn(Borrowing $b) => [
                'id'             => $b->id,
                'tanggal_pinjam' => $b->tanggal_pinjam,
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

        return Inertia::render('Admin/BorrowApprovals/Index', [
            'pending' => $pending,
            'totalPending' => $pending->count(),
        ]);
    }

    public function approve(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'menunggu_persetujuan') {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses.']);
        }

        // Check stock still available
        if ($borrowing->book->availableStock() <= 0) {
            return back()->withErrors(['error' => 'Stok buku sudah habis.']);
        }

        $borrowing->update([
            'status'         => 'dipinjam',
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(7),
        ]);

        // Decrement stock only on approval
        $borrowing->book->decrement('stok');

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

        return back()->with('success', "Permintaan pinjam \"{$bookTitle}\" dari {$memberName} ditolak.");
    }
}
