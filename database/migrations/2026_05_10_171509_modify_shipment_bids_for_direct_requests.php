<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_bids', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->change();
            $table->date('latest_pickup_date')->nullable()->change();
            $table->time('latest_pickup_time')->nullable()->change();
            $table->date('latest_delivery_date')->nullable()->change();
            $table->time('latest_delivery_time')->nullable()->change();
            $table->boolean('is_negotiable')->default(true)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_bids', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable(false)->change();
            $table->date('latest_pickup_date')->nullable(false)->change();
            $table->time('latest_pickup_time')->nullable(false)->change();
            $table->date('latest_delivery_date')->nullable(false)->change();
            $table->time('latest_delivery_time')->nullable(false)->change();
            $table->dropColumn('is_negotiable');
        });
    }
};
