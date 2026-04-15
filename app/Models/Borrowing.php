<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Setting;

class Borrowing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'book_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'status',
        'denda',
        'payment_status',
        'return_requested_at',
        'admin_return_approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_dikembalikan' => 'datetime',
        'denda' => 'integer',
        'payment_status' => 'string',
        'return_requested_at' => 'datetime',
        'admin_return_approved_at' => 'datetime',
    ];

    /**
     * Get the member that made this borrowing
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the book that was borrowed
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Check if borrowing is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->status === 'dikembalikan') {
            return false;
        }
        return Carbon::now()->greaterThan($this->tanggal_kembali);
    }

    /**
     * Calculate fine (denda) based on admin settings
     */
    public function calculateFine(): int
    {
        // Convert to date-only strings first, then parse — eliminates time-component bugs
        $returnDateStr = $this->tanggal_dikembalikan
            ? Carbon::parse($this->tanggal_dikembalikan)->toDateString()
            : Carbon::today()->toDateString();

        $returnDate = Carbon::parse($returnDateStr)->startOfDay();
        $deadline   = Carbon::parse($this->tanggal_kembali)->startOfDay();

        // Grace period
        $graceDays         = (int) Setting::get('grace_period_days', 0);
        $effectiveDeadline = $deadline->copy()->addDays($graceDays);

        if ($returnDate->lte($effectiveDeadline)) {
            return 0;
        }

        $daysLate   = abs((int) $returnDate->diffInDays($effectiveDeadline));
        $finePerDay = (int) Setting::get('fine_per_day', 1000);

        return $daysLate * $finePerDay;
    }

    /**
     * Request return - called by student
     */
    public function requestReturn(): void
    {
        $this->return_requested_at = Carbon::now();
        $this->status = 'menunggu_pengembalian';
        $this->save();
    }

    /**
     * Return the book - called after admin approval
     */
    public function returnBook(): void
    {
        $this->tanggal_dikembalikan = Carbon::now();
        $this->admin_return_approved_at = Carbon::now();
        $this->denda = $this->calculateFine();
        $this->status = $this->denda > 0 ? 'terlambat' : 'dikembalikan';
        $this->save();
        
        // Kembalikan stok buku
        $this->book->increment('stok');
    }
}
