<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Services\FonnteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FineController extends Controller
{
    /**
     * Display all fines.
     */
    public function index(Request $request): Response
    {
        $query = Borrowing::with(['member.user:id,name,email,phone', 'book'])
            ->where(function ($q) {
                $q->where('denda', '>', 0)
                  ->orWhere(function ($q2) {
                      $q2->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                         ->whereDate('tanggal_kembali', '<', now());
                  });
            });

        $status = $request->input('status');
        if ($status === 'pending') {
            $query->where('payment_status', 'pending')->where('denda', '>', 0);
        } elseif ($status === 'paid') {
            $query->where('payment_status', 'paid')->where('denda', '>', 0);
        } elseif ($status === 'unpaid') {
            $query->whereNull('payment_status');
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('member.user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhereHas('member', function ($q2) use ($search) {
                    $q2->where('no_anggota', 'like', "%{$search}%");
                });
            });
        }

        $fines = $query->latest()->paginate(10);
        
        // Calculate dynamic denda for active borrowings
        $fines->through(function ($borrowing) {
            if (in_array($borrowing->status, ['dipinjam', 'menunggu_pengembalian'])) {
                $calculated = $borrowing->calculateFine();
                // Override denda attribute dynamically for display
                $borrowing->denda = $calculated > 0 ? $calculated : $borrowing->denda;
            }
            return $borrowing;
        });

        // For stats, we need total calculation
        $allUnpaid = Borrowing::whereNull('payment_status')
            ->where(function($q) {
                $q->where('denda', '>', 0)
                  ->orWhere(function ($q2) {
                      $q2->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                         ->whereDate('tanggal_kembali', '<', now());
                  });
            })->get();
            
        $totalUnpaid = $allUnpaid->sum(function($b) {
            return in_array($b->status, ['dipinjam', 'menunggu_pengembalian']) ? $b->calculateFine() : $b->denda;
        });

        $stats = [
            'total_unpaid' => $totalUnpaid,
            'total_pending' => Borrowing::where('denda', '>', 0)->where('payment_status', 'pending')->sum('denda'),
            'total_paid' => Borrowing::where('denda', '>', 0)->where('payment_status', 'paid')->sum('denda'),
        ];

        return Inertia::render('Admin/Fines/Index', [
            'fines' => $fines,
            'stats' => $stats,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    /**
     * Confirm a fine payment.
     */
    public function confirm(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->denda <= 0) {
            return back()->withErrors(['error' => 'Tidak ada denda untuk dikonfirmasi.']);
        }

        if ($borrowing->payment_status !== 'pending') {
            return back()->withErrors(['error' => 'Denda ini belum diajukan untuk pembayaran.']);
        }

        $borrowing->update([
            'payment_status' => 'paid',
            // Keep the denda value for record purposes
        ]);

        return back()->with('success', 'Pembayaran denda berhasil dikonfirmasi.');
    }

    /**
     * Send a WhatsApp fine reminder to the student.
     */
    public function remind(Borrowing $borrowing): RedirectResponse
    {
        // Calculate dynamic denda for active borrowings if needed
        $currentDenda = $borrowing->denda;
        if (in_array($borrowing->status, ['dipinjam', 'menunggu_pengembalian'])) {
            $calculated = $borrowing->calculateFine();
            $currentDenda = $calculated > 0 ? $calculated : $borrowing->denda;
        }

        if ($currentDenda <= 0) {
            return back()->withErrors(['error' => 'Tidak ada denda untuk diingatkan.']);
        }

        if ($borrowing->payment_status === 'paid') {
            return back()->withErrors(['error' => 'Denda ini sudah lunas.']);
        }

        $borrowing->load('member.user', 'book');
        $phone = $borrowing->member->telepon ?? $borrowing->member->user->phone;

        if (!$phone) {
            return back()->withErrors(['error' => 'Siswa ini belum memiliki nomor WhatsApp terdaftar.']);
        }

        $name   = $borrowing->member->user->name;
        $judul  = $borrowing->book->judul;
        $amount = number_format($currentDenda, 0, ',', '.');

        $fonnte = new FonnteService();
        $fonnte->send($phone,
            "Halo *{$name}*! 📚\n\n"
            . "Kami ingin mengingatkan bahwa kamu masih memiliki *denda keterlambatan* yang belum dibayar.\n\n"
            . "Buku: *{$judul}*\n"
            . "Jumlah Denda: *Rp {$amount}*\n\n"
            . "Harap segera lunasi denda di perpustakaan kami.\n"
            . "Terima kasih! 🙏"
        );

        return back()->with('success', "Pengingat denda berhasil dikirim ke WhatsApp {$name}.");
    }
}
