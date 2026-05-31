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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $table->enum('payment_method', ['cash', 'gcash']);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['rental_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
