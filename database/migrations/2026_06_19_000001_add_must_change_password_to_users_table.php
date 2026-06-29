<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom 'must_change_password' ke tabel users.
     *
     * Kolom ini menandai apakah user perlu mengganti passwordnya
     * sebelum dapat mengakses sistem secara penuh.
     * Diset ke true secara default agar semua user baru wajib ganti password.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')
                  ->default(true)
                  ->after('admin_level')
                  ->comment('Tandai apakah user harus ganti password saat login pertama.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};
