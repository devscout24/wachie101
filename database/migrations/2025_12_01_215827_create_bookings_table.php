<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Dates
            $table->date('start_date');
            $table->date('end_date');

            // Guests
            $table->integer('adults')->default(0);
            $table->integer('children')->default(0);

            //  PRICING BREAKDOWN
            $table->integer('nights')->default(3)->nullable();
            $table->decimal('price_per_night', 10, 2)->nullable();
            $table->decimal('price_total', 10, 2)->nullable();
            $table->decimal('cleaning_fee', 10, 2)->default(0)->nullable();
            $table->decimal('booking_fee', 10, 2)->default(0)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();

            //  PAYMENT
            $table->string('payment_status')->default('pending')->nullable(); // pending, paid, cancelled
            $table->string('stripe_session_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
