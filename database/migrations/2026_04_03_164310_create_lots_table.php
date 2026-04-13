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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->integer('quantity');
            $table->decimal('weight', 10, 2); 
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->string('palette_type')->nullable();
            $table->string('container_type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('vehicle_weight')->nullable();
            $table->boolean('is_rolling')->nullable();
            $table->decimal('volume', 10, 4); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
