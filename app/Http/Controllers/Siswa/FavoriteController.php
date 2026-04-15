<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookFavorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $userId = $user->id;

        $favorites = BookFavorite::where('user_id', $userId)
            ->with('book')
            ->latest()
            ->get()
            ->map(fn($fav) => [
                'id'           => $fav->book->id,
                'judul'        => $fav->book->judul,
                'pengarang'    => $fav->book->pengarang,
                'penerbit'     => $fav->book->penerbit,
                'tahun_terbit' => $fav->book->tahun_terbit,
                'deskripsi'    => $fav->book->deskripsi,
                'cover_image'  => $fav->book->cover_image,
                'stok'         => $fav->book->stok,
                'available'    => $fav->book->availableStock() > 0,
                'is_favorited' => true,
            ]);

        $borrowedBooksStatus = [];
        if ($user && $user->member) {
            $borrowedBooksStatus = $user->member->borrowings()
                ->whereIn('status', ['dipinjam', 'menunggu_persetujuan', 'menunggu_pengembalian'])
                ->get(['book_id', 'status'])
                ->pluck('status', 'book_id')
                ->toArray();
        }

        return Inertia::render('Siswa/Favorites', [
            'favorites' => $favorites,
            'borrowedBooksStatus' => $borrowedBooksStatus,
        ]);
    }

    public function toggle(Book $book): RedirectResponse
    {
        $userId = auth()->id();

        $existing = BookFavorite::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Buku dihapus dari favorit.');
        }

        BookFavorite::create([
            'user_id' => $userId,
            'book_id' => $book->id,
        ]);

        return back()->with('success', 'Buku ditambahkan ke favorit!');
    }
}
