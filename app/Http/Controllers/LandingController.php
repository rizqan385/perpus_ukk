<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookFavorite;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = auth()->id();
        $query  = Book::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('pengarang', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->get()->map(function (Book $book) use ($userId) {
            return [
                'id'           => $book->id,
                'judul'        => $book->judul,
                'pengarang'    => $book->pengarang,
                'penerbit'     => $book->penerbit,
                'tahun_terbit' => $book->tahun_terbit,
                'stok'         => $book->stok,
                'cover_image'  => $book->cover_image,
                'deskripsi'    => $book->deskripsi,
                'is_favorited' => $book->isFavoritedBy($userId),
                'available'    => $book->availableStock() > 0,
            ];
        });

        $stats = [
            'total_books'   => Book::count(),
            'total_members' => \App\Models\Member::count(),
            'total_genres'  => Book::distinct('penerbit')->count('penerbit'),
        ];

        // Get borrowed books with their status for the logged-in student
        $borrowedBooksStatus = [];
        if ($userId) {
            $user = auth()->user();
            if ($user && $user->member) {
                $borrowedBooksStatus = $user->member->borrowings()
                    ->whereIn('status', ['dipinjam', 'menunggu_persetujuan', 'menunggu_pengembalian'])
                    ->get(['book_id', 'status'])
                    ->pluck('status', 'book_id')
                    ->toArray();
            }
        }

        return Inertia::render('Siswa/Home', [
            'books'               => $books,
            'stats'               => $stats,
            'search'              => $search ?? '',
            'newBooks'            => $books->take(8)->values(),
            'borrowedBooksStatus' => $borrowedBooksStatus,
        ]);
    }

    public function catalog(Request $request): Response
    {
        $userId = auth()->id();
        $query  = Book::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('pengarang', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->get()->map(function (Book $book) use ($userId) {
            return [
                'id'           => $book->id,
                'judul'        => $book->judul,
                'pengarang'    => $book->pengarang,
                'penerbit'     => $book->penerbit,
                'tahun_terbit' => $book->tahun_terbit,
                'stok'         => $book->stok,
                'cover_image'  => $book->cover_image,
                'deskripsi'    => $book->deskripsi,
                'is_favorited' => $book->isFavoritedBy($userId),
                'available'    => $book->availableStock() > 0,
            ];
        });

        $borrowedBooksStatus = [];
        if ($userId) {
            $user = auth()->user();
            if ($user && $user->member) {
                $borrowedBooksStatus = $user->member->borrowings()
                    ->whereIn('status', ['dipinjam', 'menunggu_persetujuan', 'menunggu_pengembalian'])
                    ->get(['book_id', 'status'])
                    ->pluck('status', 'book_id')
                    ->toArray();
            }
        }

        return Inertia::render('Siswa/Koleksi', [
            'books'               => $books,
            'search'              => $search ?? '',
            'borrowedBooksStatus' => $borrowedBooksStatus,
        ]);
    }

    public function show(Book $book): Response
    {
        $userId = auth()->id();

        $relatedBooks = Book::where('id', '!=', $book->id)
            ->where('pengarang', $book->pengarang)
            ->orWhere('penerbit', $book->penerbit)
            ->where('id', '!=', $book->id)
            ->limit(4)
            ->get()
            ->map(fn(Book $b) => [
                'id'          => $b->id,
                'judul'       => $b->judul,
                'pengarang'   => $b->pengarang,
                'cover_image' => $b->cover_image,
                'stok'        => $b->stok,
            ]);

        // Check if the current user already borrowed this book (active)
        $alreadyBorrowed = false;
        $pendingBorrow   = false;
        if ($userId) {
            $member = auth()->user()->member;
            if ($member) {
                $alreadyBorrowed = $member->borrowings()
                    ->where('book_id', $book->id)
                    ->where('status', 'dipinjam')
                    ->exists();
                $pendingBorrow = $member->borrowings()
                    ->where('book_id', $book->id)
                    ->where('status', 'menunggu_persetujuan')
                    ->exists();
            }
        }

        return Inertia::render('Siswa/BookDetail', [
            'book' => [
                'id'           => $book->id,
                'judul'        => $book->judul,
                'pengarang'    => $book->pengarang,
                'penerbit'     => $book->penerbit,
                'tahun_terbit' => $book->tahun_terbit,
                'isbn'         => $book->isbn,
                'stok'         => $book->stok,
                'cover_image'  => $book->cover_image,
                'deskripsi'    => $book->deskripsi,
                'is_favorited' => $book->isFavoritedBy($userId),
                'available'    => $book->availableStock() > 0,
            ],
            'relatedBooks'   => $relatedBooks,
            'alreadyBorrowed' => $alreadyBorrowed,
            'pendingBorrow'   => $pendingBorrow,
        ]);
    }
}
