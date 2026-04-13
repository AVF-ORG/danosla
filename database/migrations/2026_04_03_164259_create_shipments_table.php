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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->text('comment')->nullable();
            $table->decimal('total_value', 12, 2)->nullable();
            $table->decimal('total_volume', 12, 4);
            $table->decimal('total_weight', 12, 2);
            $table->string('pickup_address');
            $table->string('pickup_type');
            $table->json('pickup_options')->nullable();
            $table->string('delivery_address');
            $table->string('delivery_type');
            $table->json('delivery_options')->nullable();
            $table->date('latest_pickup_date');
            $table->date('latest_delivery_date');
            $table->json('requirements')->nullable(); 
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
