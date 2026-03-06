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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();

            // $table->unsignedBigInteger('property_ref_id')->nullable();
            // $table->unsignedBigInteger('room_ref_id')->nullable();

            $table->json('multiple_image')->nullable();
            $table->string('title');
            $table->string('location');
            
            $table->decimal('latitude',10,8)->default(0);
            $table->decimal('longitude',10,8)->default(0);
            
            $table->text('map_link')->nullable();
            $table->foreignId('rating')->nullable();
            $table->foreignId('total_reviews')->default(0)->nullable();
            $table->float('price')->default(0);
            $table->float('refundable_deposit')->default(0);
            $table->float('cleaning_fee')->default(0);
            $table->float('booking_fee')->default(0);

            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('max_guests')->default(0);
            $table->integer('max_children')->default(0);
            $table->foreignId('amenity_id')->nullable();

            $table->text('description')->nullable();
            $table->text('local_area')->nullable();

            $table->string('image')->default('default.png');
            $table->boolean('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();

            // $table->index('property_ref_id');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
