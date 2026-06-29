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
        Schema::table('letters', function (Blueprint $table) {
            $table->string('status')->default('pending_admin_1')->comment('Status surat')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // Reverting back to enum might cause data loss if there are other string values, 
            // but we'll put the original definition just in case.
            // Note: ENUM changes using Doctrine DBAL might not be perfectly reversible without losing data.
            $table->enum('status', ['pending', 'revision', 'approved', 'rejected'])->default('pending')->comment('Status surat')->change();
        });
    }
};
