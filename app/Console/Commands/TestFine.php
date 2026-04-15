<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestFine extends Command
{
    protected $signature   = 'test:fine
                              {--days=5 : Berapa hari terlambat}
                              {--member= : ID member (default: member pertama)}
                              {--book=   : ID buku (default: buku pertama)}
                              {--cleanup : Hapus data test setelah selesai}';

    protected $description = 'Buat transaksi terlambat untuk test fitur denda';

    public function handle(): int
    {
        $daysLate  = (int) $this->option('days');
        $cleanup   = $this->option('cleanup');

        // ── Cari Member ──────────────────────────────────
        $member = $this->option('member')
            ? Member::find($this->option('member'))
            : Member::with('user')->where('status', 'aktif')->first();

        if (!$member) {
            $this->error('Tidak ada member aktif. Jalankan seeder terlebih dahulu.');
            return self::FAILURE;
        }

        // ── Cari Buku ────────────────────────────────────
        $book = $this->option('book')
            ? Book::find($this->option('book'))
            : Book::where('stok', '>', 0)->first();

        if (!$book) {
            $this->error('Tidak ada buku dengan stok tersedia.');
            return self::FAILURE;
        }

        $finePerDay  = (int) Setting::get('fine_per_day', 1000);
        $graceDays   = (int) Setting::get('grace_period_days', 0);
        $expectedFine = max(0, ($daysLate - $graceDays)) * $finePerDay;

        $this->newLine();
        $this->line('═══════════════════════════════════════════════════');
        $this->info('  🧪  TEST DENDA — PERPUSTAKAAN');
        $this->line('═══════════════════════════════════════════════════');

        $this->table(['Setting', 'Nilai'], [
            ['Denda per hari',  'Rp ' . number_format($finePerDay, 0, ',', '.')],
            ['Grace period',    "{$graceDays} hari"],
            ['Hari terlambat',  "{$daysLate} hari"],
            ['Ekspektasi denda','Rp ' . number_format($expectedFine, 0, ',', '.')],
        ]);

        // ── Buat Borrowing masa lalu ──────────────────────
        $tanggalPinjam  = Carbon::now()->subDays($daysLate + 7);
        $tanggalKembali = Carbon::now()->subDays($daysLate);

        $this->line("📚 Buku    : <fg=cyan>{$book->judul}</>");
        $this->line("👤 Member  : <fg=cyan>{$member->user->name}</> ({$member->no_anggota})");
        $this->line("📅 Pinjam  : <fg=cyan>{$tanggalPinjam->format('d/m/Y')}</>");
        $this->line("📅 Deadline: <fg=cyan>{$tanggalKembali->format('d/m/Y')}</> (sudah lewat {$daysLate} hari)");
        $this->newLine();

        /** @var Borrowing $borrowing */
        $borrowing = Borrowing::create([
            'member_id'      => $member->id,
            'book_id'        => $book->id,
            'tanggal_pinjam' => $tanggalPinjam->toDateString(),
            'tanggal_kembali'=> $tanggalKembali->toDateString(),
            'status'         => 'dipinjam',
        ]);
        $book->decrement('stok');

        $this->line('✅ Transaksi dibuat (ID: ' . $borrowing->id . ', status: <fg=yellow>dipinjam</>)');

        // ── Proses pengembalian ───────────────────────────
        $borrowing->returnBook();
        $borrowing->refresh();

        $this->line("✅ Buku dikembalikan (hari ini)");
        $this->newLine();

        // ── Hasil ────────────────────────────────────────
        $pass = $borrowing->denda === $expectedFine;

        $this->line('═══════════════════════════════════════════════════');
        $this->info('  📊  HASIL TEST');
        $this->line('═══════════════════════════════════════════════════');

        $this->table(['Item', 'Nilai'], [
            ['Status akhir',      "<fg=cyan>{$borrowing->status}</>"],
            ['Denda tersimpan',   '<fg=' . ($borrowing->denda > 0 ? 'red' : 'green') . '>Rp ' . number_format($borrowing->denda, 0, ',', '.') . '</>'],
            ['Ekspektasi',        'Rp ' . number_format($expectedFine, 0, ',', '.')],
            ['Payment status',    $borrowing->payment_status ?? '<null — belum dibayar>'],
        ]);

        if ($pass) {
            $this->info('  ✅  PASS — Denda dihitung dengan benar!');
        } else {
            $this->error('  ❌  FAIL — Denda tidak sesuai ekspektasi!');
            $this->line("     Ekspektasi : Rp " . number_format($expectedFine, 0, ',', '.'));
            $this->line("     Aktual     : Rp " . number_format($borrowing->denda, 0, ',', '.'));
        }

        // ── Test: member tidak bisa pinjam ───────────────
        $this->newLine();
        $this->line('═══════════════════════════════════════════════════');
        $this->info('  🔒  TEST BLOKIR PEMINJAMAN');
        $this->line('═══════════════════════════════════════════════════');

        $unpaidFines = $member->borrowings()
            ->where('denda', '>', 0)
            ->whereNull('payment_status')
            ->sum('denda');

        if ($unpaidFines > 0) {
            $this->info('  ✅  PASS — Member DIBLOKIR (denda: Rp ' . number_format($unpaidFines, 0, ',', '.') . ')');
        } else {
            $this->error('  ❌  FAIL — Member tidak diblokir padahal ada denda!');
        }

        // ── Cleanup ──────────────────────────────────────
        if ($cleanup) {
            $borrowing->delete();
            $book->increment('stok');
            $this->newLine();
            $this->line('🗑️  Data test sudah dihapus (--cleanup)');
        } else {
            $this->newLine();
            $this->comment('💡 Jalankan dengan --cleanup untuk hapus data test otomatis.');
            $this->comment("   Atau cek di browser: /admin/transactions dan /admin/fines");
            $this->comment("   Borrowing ID: {$borrowing->id}");
        }

        $this->newLine();
        return $pass ? self::SUCCESS : self::FAILURE;
    }
}
