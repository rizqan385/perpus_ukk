<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'no_anggota',
        'kelas',
        'alamat',
        'telepon',
        'foto',
        'tanggal_lahir',
        'jenis_kelamin',
        'status',
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) return null;
        return asset('storage/' . $this->foto);
    }

    /**
     * Get the user that owns the member profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the borrowings for this member
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Check if member is active
     */
    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Get active borrowings
     */
    public function activeBorrowings(): HasMany
    {
        return $this->borrowings()->where('status', 'dipinjam');
    }

    /**
     * Generate unique member number
     */
    public static function generateMemberNumber(): string
    {
        $year = date('Y');
        $lastMember = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        
        if ($lastMember && preg_match('/MBR\d{4}(\d{4})/', $lastMember->no_anggota, $matches)) {
            $count = intval($matches[1]) + 1;
        } else {
            $count = self::whereYear('created_at', $year)->count() + 1; // Fallback
        }
        
        return 'MBR' . $year . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
