<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin Account
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Sample Siswa Account (with membership)
        $siswa = User::create([
            'name' => 'Siswa Demo',
            'email' => 'siswa@perpus.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'email_verified_at' => now(),
        ]);

        Member::create([
            'user_id' => $siswa->id,
            'no_anggota' => Member::generateMemberNumber(),
            'kelas' => 'XII RPL 1',
            'alamat' => 'Jl. Contoh No. 123',
            'telepon' => '08123456789',
            'status' => 'aktif',
        ]);

        // Create Sample Books
        $books = [
            [
                'judul' => 'Laskar Pelangi',
                'pengarang' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'tahun_terbit' => 2005,
                'isbn' => '9789793062792',
                'stok' => 5,
                'deskripsi' => 'Novel tentang perjuangan anak-anak di Belitung untuk mendapatkan pendidikan.',
            ],
            [
                'judul' => 'Bumi Manusia',
                'pengarang' => 'Pramoedya Ananta Toer',
                'penerbit' => 'Hasta Mitra',
                'tahun_terbit' => 1980,
                'isbn' => '9789799571204',
                'stok' => 3,
                'deskripsi' => 'Novel pertama dari Tetralogi Buru yang mengisahkan Minke.',
            ],
            [
                'judul' => 'Negeri 5 Menara',
                'pengarang' => 'Ahmad Fuadi',
                'penerbit' => 'Gramedia',
                'tahun_terbit' => 2009,
                'isbn' => '9789792234275',
                'stok' => 4,
                'deskripsi' => 'Novel tentang kehidupan di pesantren dan perjuangan menggapai mimpi.',
            ],
            [
                'judul' => 'Pulang',
                'pengarang' => 'Tere Liye',
                'penerbit' => 'Republika',
                'tahun_terbit' => 2015,
                'isbn' => '9786020324487',
                'stok' => 6,
                'deskripsi' => 'Novel keluarga yang mengharukan dari penulis terkenal Indonesia.',
            ],
            [
                'judul' => 'Filosofi Teras',
                'pengarang' => 'Henry Manampiring',
                'penerbit' => 'Kompas',
                'tahun_terbit' => 2018,
                'isbn' => '9786024125653',
                'stok' => 7,
                'deskripsi' => 'Buku tentang filosofi Stoikisme untuk kehidupan modern.',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
