<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key'         => 'fine_per_day',
                'value'       => '1000',
                'description' => 'Denda keterlambatan per hari (Rupiah)',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'grace_period_days',
                'value'       => '0',
                'description' => 'Jumlah hari bebas denda setelah tenggat (grace period)',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'max_borrow_days',
                'value'       => '7',
                'description' => 'Maksimal hari peminjaman buku',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
