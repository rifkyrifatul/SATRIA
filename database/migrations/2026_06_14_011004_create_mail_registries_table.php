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
        Schema::create('mail_registries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['masuk', 'keluar']);
            $table->string('reference_number');
            $table->date('date');
            $table->string('origin_destination');
            $table->string('subject');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_registries');
    }
};
