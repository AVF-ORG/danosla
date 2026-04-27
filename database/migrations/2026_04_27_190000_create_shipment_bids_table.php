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
        Schema::create('shipment_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The carrier
            $table->decimal('price', 12, 2);
            $table->date('latest_pickup_date');
            $table->time('latest_pickup_time');
            $table->date('latest_delivery_date');
            $table->time('latest_delivery_time');
            $table->string('status')->default('pending'); // pending, accepted, rejected, cancelled, countered
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_bids');
    }
};
