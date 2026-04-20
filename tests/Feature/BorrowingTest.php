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
        
        // Create an admin user
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
        // Create a student user with member
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

        // Create a book with 5 stock
        $book = Book::create([
            'judul' => 'Test Book',
            'pengarang' => 'Test Author',
            'penerbit' => 'Test Publisher',
            'tahun_terbit' => 2024,
            'stok' => 5,
        ]);

        $initialStock = $book->stok;

        // Borrow the book
        $this->actingAs($user)
            ->post('/siswa/borrow', ['book_id' => $book->id]);

        // Refresh the book from database
        $this->assertEquals(5, $book->stok);

        $borrowing = Borrowing::first();
        $this->assertEquals('menunggu_persetujuan', $borrowing->status);

        // 2. Admin approves
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)
            ->post("/admin/borrow-approvals/{$borrowing->id}/approve");

        $book->refresh();
        $this->assertEquals(4, $book->stok);
    }

    /** @test */
    public function stock_does_not_increase_until_return_is_approved()
    {
        // Create a student user with member
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

        // Create a book with reduced stock (simulating after borrow)
        $book = Book::create([
            'judul' => 'Test Book',
            'pengarang' => 'Test Author',
            'penerbit' => 'Test Publisher',
            'tahun_terbit' => 2024,
            'stok' => 4, // Already borrowed one
        ]);

        // Create an active borrowing
        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(7),
            'status' => 'dipinjam',
        ]);

        $stockBeforeReturn = $book->stok;

        // Request return
        $borrowing->requestReturn();

        // Refresh the database models
        $book->refresh();
        $borrowing->refresh();

        // Assert stock did NOT increase yet and status is pending approval
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

        // Create a borrowing that is 3 days overdue
        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now()->subDays(10),
            'tanggal_kembali' => Carbon::now()->subDays(3), // Due date was 3 days ago
            'status' => 'dipinjam',
        ]);

        // Return the book
        $borrowing->returnBook();

        // Assert fine is calculated (3 days * Rp 1000 = Rp 3000)
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

        // Create a borrowing that is still within the due date
        $borrowing = Borrowing::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::now()->subDays(5),
            'tanggal_kembali' => Carbon::now()->addDays(2), // Due date is 2 days from now
            'status' => 'dipinjam',
        ]);

        // Return the book
        $borrowing->returnBook();

        // Assert no fine
        $this->assertEquals(0, $borrowing->denda);
        $this->assertEquals('dikembalikan', $borrowing->status);
    }
}
