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
        Schema::create('letter_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('letter_id')->constrained('letters')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_type'); // e.g. original, staff_revision, progar_correction, pekas_correction, setum_final, superadmin_force_final
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_attachments');
    }
};
