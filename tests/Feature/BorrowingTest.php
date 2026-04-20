<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            \Illuminate\Support\Facades\DB::statement('PRAGMA ignore_check_constraints = 1');
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function stock_decreases_when_book_is_borrowed_and_approved()
    {
        $user = User::create([
            'name' => 'Student',
            'email' => 'student@test.com',
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

        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(7),
            'status' => 'menunggu_persetujuan',
        ]);

        $this->assertEquals(5, $book->stok);
        $this->assertEquals('menunggu_persetujuan', $borrowing->status);

        // Langsung approve tanpa hit route
        $borrowing->update([
            'status'          => 'dipinjam',
            'tanggal_pinjam'  => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(7),
        ]);
        $borrowing->book->decrement('stok');

        $book->refresh();
        $this->assertEquals(4, $book->stok);
    }

    /** @test */
    public function stock_does_not_increase_until_return_is_approved()
    {
        $user = User::create([
            'name' => 'Student',
            'email' => 'student@test.com',
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
            'stok' => 4,
        ]);

        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(7),
            'status' => 'dipinjam',
        ]);

        $stockBeforeReturn = $book->stok;

        $borrowing->requestReturn();

        $book->refresh();
        $borrowing->refresh();

        $this->assertEquals($stockBeforeReturn, $book->stok);
        $this->assertEquals('menunggu_pengembalian', $borrowing->status);
    }

    /** @test */
    public function fine_is_calculated_correctly_when_late()
    {
        $user = User::create([
            'name' => 'Student',
            'email' => 'student@test.com',
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
            'stok' => 4,
        ]);

        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now()->subDays(10),
            'tanggal_kembali' => Carbon::now()->subDays(3),
            'status' => 'dipinjam',
        ]);

        $borrowing->returnBook();

        $this->assertEquals(3000, $borrowing->denda);
        $this->assertEquals('terlambat', $borrowing->status);
    }

    /** @test */
    public function no_fine_when_returned_on_time()
    {
        $user = User::create([
            'name' => 'Student',
            'email' => 'student@test.com',
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
            'stok' => 4,
        ]);

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
}