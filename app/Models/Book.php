<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'stok',
        'cover_image',
        'deskripsi',
    ];

    /**
     * Get the borrowings for this book
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Check if book is available for borrowing
     */
    public function isAvailable(): bool
    {
        return $this->stok > 0;
    }

    /**
     * Get current borrowed count
     */
    public function borrowedCount(): int
    {
        return $this->borrowings()->where('status', 'dipinjam')->count();
    }

    /**
     * Get available stock (now same as physical stock since we decrement/increment)
     */
    public function availableStock(): int
    {
        return $this->stok;
    }
}
