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
        Schema::create('letters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('category_id')->nullable();
            $table->string('letter_number')->nullable()->comment('Nomor surat, bisa diisi manual atau auto-generate');
            $table->string('title')->comment('Judul / perihal surat');
            $table->string('file_path')->comment('Path file surat yang di-upload');
            $table->string('final_file_path')->nullable()->comment('Path file PDF final');
            $table->string('file_extension', 10)->comment('Ekstensi file: pdf, doc, docx, dll');
            $table->enum('status', ['pending', 'revision', 'approved', 'rejected'])->default('pending')->comment('Status surat');
            $table->string('last_rejected_status')->nullable();
            $table->boolean('is_force_approved')->default(false);
            $table->integer('version')->default(1);
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->comment('User yang mengajukan surat');
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Admin/user terakhir yang mengubah status surat');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
