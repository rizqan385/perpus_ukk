<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->timestamp('return_requested_at')->nullable()->after('tanggal_dikembalikan');
            $table->timestamp('admin_return_approved_at')->nullable()->after('return_requested_at');
        });

        // Update the status enum to include 'menunggu_pengembalian'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE borrowings MODIFY COLUMN status ENUM('dipinjam', 'menunggu_pengembalian', 'dikembalikan', 'terlambat') DEFAULT 'dipinjam'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['return_requested_at', 'admin_return_approved_at']);
        });

        // Revert status enum to original values
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE borrowings MODIFY COLUMN status ENUM('dipinjam', 'dikembalikan', 'terlambat') DEFAULT 'dipinjam'");
        }
    }
};
