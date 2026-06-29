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
        Schema::create('renbut_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renbut_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('estimated_unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renbut_items');
    }
};
