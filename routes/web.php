<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Siswa\BorrowingController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\MemberRegistrationController;
use App\Http\Controllers\Siswa\ReturnController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    // Redirect authenticated users to their dashboard
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('siswa.dashboard');
    }
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// Old dashboard route - redirect based on role
Route::get('dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::prefix('admin')
    ->middleware(['auth', 'verified', RoleMiddleware::class . ':admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Books Management
        Route::resource('books', BookController::class);
        
        // Members Management
        Route::resource('members', MemberController::class);
        
        // Transactions Management
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::post('transactions/{transaction}/return', [TransactionController::class, 'returnBook'])->name('transactions.return');
        Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    });

// Siswa (Student) Routes
Route::prefix('siswa')
    ->middleware(['auth', 'verified', RoleMiddleware::class . ':siswa'])
    ->name('siswa.')
    ->group(function () {
        Route::get('/', [SiswaDashboardController::class, 'index'])->name('dashboard');
        
        // Member Registration
        Route::get('register', [MemberRegistrationController::class, 'create'])->name('register');
        Route::post('register', [MemberRegistrationController::class, 'store'])->name('register.store');
        
        // Borrowing
        Route::get('borrow', [BorrowingController::class, 'index'])->name('borrow');
        Route::post('borrow', [BorrowingController::class, 'store'])->name('borrow.store');
        
        // Returns
        Route::get('returns', [ReturnController::class, 'index'])->name('returns');
        Route::post('returns/{borrowing}', [ReturnController::class, 'returnBook'])->name('returns.store');
    });

require __DIR__.'/settings.php';
