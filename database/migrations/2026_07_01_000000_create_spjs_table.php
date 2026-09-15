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
        Schema::create('spjs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('spj_number')->nullable();
            $table->string('type')->default('SPJ Kegiatan');
            $table->string('file_path');
            $table->string('file_original_name');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('admin_level')->nullable(); // admin_1 (PROGAR), admin_2 (PEKAS), etc.
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spjs');
    }
};
