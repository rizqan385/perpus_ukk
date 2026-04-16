<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('telepon');
            $table->date('tanggal_lahir')->nullable()->after('foto');
            $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['foto', 'tanggal_lahir', 'jenis_kelamin']);
        });
    }
};
