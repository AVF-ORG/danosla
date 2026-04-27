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
        Schema::create('bid_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bid_id')->constrained('shipment_bids')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The sender
            $table->foreignId('parent_id')->nullable()->constrained('bid_messages')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid_messages');
    }
};
