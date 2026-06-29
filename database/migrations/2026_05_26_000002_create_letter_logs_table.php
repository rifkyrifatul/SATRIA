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
        Schema::create('letter_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('letter_id')
                  ->constrained('letters')
                  ->onDelete('cascade')
                  ->comment('Surat yang terkait dengan log ini');
            $table->enum('action', ['submit', 'approve', 'request_revision', 'reject', 'force_approve'])->comment('Jenis aksi yang dilakukan');
            $table->text('notes')->nullable()->comment('Catatan tambahan dari user, wajib diisi saat request_revision');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->comment('User yang melakukan aksi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_logs');
    }
};
