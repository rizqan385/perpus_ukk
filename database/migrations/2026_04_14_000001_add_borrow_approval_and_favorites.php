<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add menunggu_persetujuan to borrowings status enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE borrowings MODIFY COLUMN status ENUM('menunggu_persetujuan','dipinjam', 'menunggu_pengembalian', 'dikembalikan', 'terlambat') DEFAULT 'dipinjam'");
        }

        // Create book_favorites table
        Schema::create('book_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_favorites');

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE borrowings MODIFY COLUMN status ENUM('dipinjam', 'menunggu_pengembalian', 'dikembalikan', 'terlambat') DEFAULT 'dipinjam'");
        }
    }
};
