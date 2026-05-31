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
        Schema::create('clothing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clothing_category_id')
                ->constrained('clothing_categories')
                ->restrictOnDelete();
            $table->string('name');
            $table->decimal('rental_price', 10, 2);
            $table->string('color')->nullable();
            $table->string('size', 20)->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['available', 'reserved', 'rented', 'maintenance'])->default('available');
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clothing_items');
    }
};
