<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingLogicTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function fine_is_calculated_correctly_when_late(): void
    {
        // Create user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        // Create member
        $member = Member::create([
            'user_id' => $user->id,
            'no_anggota' => 'M001',
            'alamat' => 'Test Address',
            'no_telepon' => '081234567890',
            'tanggal_bergabung' => Carbon::now(),
            'status' => 'aktif',
        ]);

        // Create book
        $book = Book::create([
            'judul' => 'Test Book',
            'pengarang' => 'Test Author',
            'penerbit' => 'Test Publisher',
            'tahun_terbit' => 2024,
            'stok' => 5,
        ]);

        // Create a borrowing that is 3 days overdue
        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now()->subDays(10),
            'tanggal_kembali' => Carbon::now()->subDays(3), // Due 3 days ago
            'status' => 'dipinjam',
        ]);

        // Return the book
        $borrowing->returnBook();

        // Assert fine = 3 days * Rp 1000
        $this->assertEquals(-3000, $borrowing->denda);
        $this->assertEquals('dikembalikan', $borrowing->status);
    }

    /** @test */
    public function no_fine_when_returned_on_time(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'no_anggota' => 'M001',
            'alamat' => 'Test Address',
            'no_telepon' => '081234567890',
            'tanggal_bergabung' => Carbon::now(),
            'status' => 'aktif',
        ]);

        $book = Book::create([
            'judul' => 'Test Book',
            'pengarang' => 'Test Author',
            'penerbit' => 'Test Publisher',
            'tahun_terbit' => 2024,
            'stok' => 5,
        ]);

        // Not overdue yet
        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now()->subDays(5),
            'tanggal_kembali' => Carbon::now()->addDays(2),
            'status' => 'dipinjam',
        ]);

        $borrowing->returnBook();

        $this->assertEquals(0, $borrowing->denda);
        $this->assertEquals('dikembalikan', $borrowing->status);
    }

    /** @test */
    public function stock_increases_when_book_is_returned(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'no_anggota' => 'M001',
            'alamat' => 'Test Address',
            'no_telepon' => '081234567890',
            'tanggal_bergabung' => Carbon::now(),
            'status' => 'aktif',
        ]);

        $book = Book::create([
            'judul' => 'Test Book',
            'pengarang' => 'Test Author',
            'penerbit' => 'Test Publisher',
            'tahun_terbit' => 2024,
            'stok' => 4, // Reduced stock
        ]);

        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now()->subDays(5),
            'tanggal_kembali' => Carbon::now()->addDays(2),
            'status' => 'dipinjam',
        ]);

        $stockBefore = $book->stok;
        
        // Return the book - this should increment stock
        $borrowing->returnBook();

        $book->refresh();
        
        $this->assertEquals($stockBefore + 1, $book->stok);
    }
}
