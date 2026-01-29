<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_dikembalikan' => 'date',
        'denda' => 'decimal:2',
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
     * Calculate fine (denda) - Rp 1000 per day
     */
    public function calculateFine(): float
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        $returnDate = $this->tanggal_dikembalikan ?? Carbon::now();
        $daysLate = $returnDate->diffInDays($this->tanggal_kembali);
        
        return $daysLate * 1000; // Rp 1000 per day
    }

    /**
     * Return the book
     */
    public function returnBook(): void
    {
        $this->tanggal_dikembalikan = Carbon::now();
        $this->denda = $this->calculateFine();
        $this->status = $this->denda > 0 ? 'terlambat' : 'dikembalikan';
        $this->save();
    }
}
