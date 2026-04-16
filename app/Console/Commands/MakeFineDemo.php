<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;
use App\Models\User;
use Carbon\Carbon;

class MakeFineDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'denda:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bikin satu peminjaman siswa demo jadi telat biar kena denda';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Cari user siswa demo (cari yang role-nya siswa)
        $user = User::where('role', 'siswa')->first();

        if (!$user || !$user->member) {
            $this->error('Gagal nemu user siswa yang sudah jadi member!');
            return;
        }

        // 2. Cari peminjaman dia yang statusnya 'dipinjam'
        $borrowing = Borrowing::where('member_id', $user->member->id)
            ->where('status', 'dipinjam')
            ->first();

        if (!$borrowing) {
            $this->info('User ' . $user->name . ' nggak lagi pinjam buku. Lagi gua pinjemin manual ya...');
            
            // Pinjemin buku random kalau dia lagi nggak pinjam
            $book = \App\Models\Book::where('stok', '>', 0)->first();
            $borrowing = Borrowing::create([
                'member_id' => $user->member->id,
                'book_id' => $book->id,
                'tanggal_pinjam' => now()->subDays(10),
                'tanggal_kembali' => now()->subDays(3), // Sudah telat 3 hari
                'status' => 'dipinjam',
            ]);
            $book->decrement('stok');
        } else {
            // Kalau dia emang lagi pinjam, kita set jadi telat
            $borrowing->update([
                'tanggal_kembali' => now()->subDays(5), // Telat 5 hari
            ]);
        }

        $this->success('MANTAP! Akun ' . $user->name . ' sekarang punya buku telat dikembalikan.');
        $this->info('Silakan login sebagai siswa ini, terus klik menu Kembalikan Buku buat liat dendanya.');
    }

    private function success($msg)
    {
         $this->output->writeln("<info>✔</info> <fg=green>$msg</>");
    }
}
