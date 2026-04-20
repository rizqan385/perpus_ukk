<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowApprovalController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FineController as AdminFineController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Siswa\AuthController as SiswaAuthController;
use App\Http\Controllers\Siswa\BorrowingController;
use App\Http\Controllers\Siswa\CardController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\FavoriteController;
use App\Http\Controllers\Siswa\FineController as SiswaFineController;
use App\Http\Controllers\Siswa\MemberRegistrationController;
use App\Http\Controllers\Siswa\ReturnController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SiswaAuthMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Services\FonnteService;

// ─── Public Landing ───────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/koleksi-buku', [LandingController::class, 'catalog'])->name('books.catalog');
Route::get('/books/{book}', [LandingController::class, 'show'])->name('books.show');

// Student-themed login/register pages (renders different Vue layout)
Route::get('/siswa/masuk', function () {
    return Inertia::render('auth/SiswaLogin', [
        'canResetPassword' => true,
        'status' => session('status'),
    ]);
})->middleware('guest')->name('siswa.login.page');
Route::post('/siswa/masuk', [SiswaAuthController::class, 'login'])->middleware('guest')->name('siswa.login.submit');

Route::get('/siswa/daftar', function () {
    return Inertia::render('auth/SiswaRegister');
})->middleware('guest')->name('siswa.register.page');
Route::post('/siswa/daftar', [SiswaAuthController::class, 'register'])->middleware('guest')->name('siswa.register.submit');

Route::get('/siswa/verifikasi-otp', function () {
    if (!session()->has('registration_data')) {
        return redirect()->route('siswa.register.page')->withErrors(['error' => 'Sesi tidak valid, silakan daftar ulang.']);
    }
    return Inertia::render('auth/SiswaOtp', [
        'phone' => session('registration_data.phone'),
        'email' => session('registration_data.email'),
        'success' => session('success'),
        'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : (object) [],
    ]);
})->middleware('guest')->name('siswa.otp.page');
Route::post('/siswa/verifikasi-otp', [SiswaAuthController::class, 'verifyOtp'])->middleware('guest')->name('siswa.otp.submit');
Route::post('/siswa/resend-otp', [SiswaAuthController::class, 'resendOtp'])->middleware('guest')->name('siswa.otp.resend');
Route::post('/siswa/resend-otp-email', [SiswaAuthController::class, 'resendOtpEmail'])->middleware('guest')->name('siswa.otp.resend.email');

Route::get('/siswa/lupa-password', function () {
    return Inertia::render('auth/SiswaForgotPassword', [
        'status' => session('status'),
    ]);
})->middleware('guest')->name('siswa.password.request');

// ─── Legacy redirect ──────────────────────────────────────────────
Route::get('dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Admin Routes ─────────────────────────────────────────────────
Route::prefix('admin')
    ->middleware(['auth', 'verified', RoleMiddleware::class . ':admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Books Management
        Route::resource('books', BookController::class);

        // Members Management
        Route::get('members/cards', [MemberController::class, 'cards'])->name('members.cards');
        Route::resource('members', MemberController::class);
        Route::get('members/{member}/card', [MemberController::class, 'card'])->name('members.card');

        // Transactions Management
        Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::post('transactions/{transaction}/return', [TransactionController::class, 'returnBook'])->name('transactions.return');
        Route::post('transactions/{transaction}/approve-return', [TransactionController::class, 'approveReturn'])->name('transactions.approve-return');
        Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

        // Approvals (Borrow + Return)
        Route::get('borrow-approvals', [BorrowApprovalController::class, 'index'])->name('borrow-approvals.index');
        Route::post('borrow-approvals/{borrowing}/approve', [BorrowApprovalController::class, 'approve'])->name('borrow-approvals.approve');
        Route::post('borrow-approvals/{borrowing}/reject', [BorrowApprovalController::class, 'reject'])->name('borrow-approvals.reject');
        Route::post('borrow-approvals/{borrowing}/approve-return', [BorrowApprovalController::class, 'approveReturn'])->name('borrow-approvals.approve-return');

        // Fines Management
        Route::get('fines', [AdminFineController::class, 'index'])->name('fines.index');
        Route::post('fines/{borrowing}/confirm', [AdminFineController::class, 'confirm'])->name('fines.confirm');
        Route::post('fines/{borrowing}/remind', [AdminFineController::class, 'remind'])->name('fines.remind');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

// ─── Siswa (Student) Routes ───────────────────────────────────────
Route::prefix('siswa')
    ->middleware([SiswaAuthMiddleware::class])
    ->name('siswa.')
    ->group(function () {
        Route::get('/', [SiswaDashboardController::class, 'index'])->name('dashboard');

        // Member Registration
        Route::get('register', [MemberRegistrationController::class, 'create'])->name('register');
        Route::post('register', [MemberRegistrationController::class, 'store'])->name('register.store');

        // Borrowing
        Route::post('borrow', [BorrowingController::class, 'store'])->name('borrow.store');

        // Returns
        Route::get('returns', [ReturnController::class, 'index'])->name('returns');
        Route::post('returns/{borrowing}/request', [ReturnController::class, 'requestReturn'])->name('returns.request');

        // Fines
        Route::get('fines', [SiswaFineController::class, 'index'])->name('fines');
        Route::post('fines/{borrowing}/pay', [SiswaFineController::class, 'pay'])->name('fines.pay');

        // Favorites
        Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites');
        Route::post('favorites/{book}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

        // Member Card
        Route::get('kartu', [CardController::class, 'show'])->name('card');

        // Profile
        Route::get('profile', [\App\Http\Controllers\Siswa\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile', [\App\Http\Controllers\Siswa\ProfileController::class, 'update'])->name('profile.update');
        Route::get('success-update', [\App\Http\Controllers\Siswa\ProfileController::class, 'success'])->name('profile.success');

        // Midtrans Payment
        Route::post('payment/{borrowing}/token', [\App\Http\Controllers\Siswa\PaymentController::class, 'getSnapToken'])->name('payment.token');
        Route::get('payment/{borrowing}/success', [\App\Http\Controllers\Siswa\PaymentController::class, 'successPayment'])->name('payment.success');
    });

Route::post('/payment/midtrans/notification', [\App\Http\Controllers\Siswa\PaymentController::class, 'handleNotification'])->name('payment.notification');

require __DIR__ . '/settings.php';

// ─── Route Rahasia Demo Denda (Hanya untuk testing Railway) ────────
Route::get('/tanam-denda-rahasia-ukom-2024', function () {
    $user = \App\Models\User::where('role', 'siswa')->first();

    if (!$user || !$user->member) {
        return "Gagal: User siswa belum ada atau belum daftar member.";
    }

    $book = \App\Models\Book::first();
    if (!$book)
        return "Gagal: Belum ada data buku.";

    \App\Models\Borrowing::create([
        'member_id' => $user->member->id,
        'book_id' => $book->id,
        'tanggal_pinjam' => now()->subDays(20),
        'tanggal_kembali' => now()->subDays(10),
        'tanggal_dikembalikan' => now()->subDays(2),
        'status' => 'dikembalikan',
        'denda' => 50000,
        'payment_status' => null
    ]);

    return "MANTAP! Denda Rp 50.000 berhasil ditanam di akun: " . $user->name . ". Silakan login dan cek menu denda.";
});
