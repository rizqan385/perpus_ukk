<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

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

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(BookFavorite::class);
    }

    public function isAvailable(): bool
    {
        return $this->stok > 0;
    }

    public function borrowedCount(): int
    {
        return $this->borrowings()->where('status', 'dipinjam')->count();
    }

    public function availableStock(): int
    {
        return $this->stok;
    }

    public function isFavoritedBy(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}
