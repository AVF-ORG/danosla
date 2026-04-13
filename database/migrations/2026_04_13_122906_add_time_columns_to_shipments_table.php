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
        Schema::table('shipments', function (Blueprint $table) {
            $table->time('latest_pickup_time')->nullable()->after('latest_pickup_date');
            $table->time('pickup_notify_time')->nullable()->after('latest_pickup_time');
            $table->time('latest_delivery_time')->nullable()->after('latest_delivery_date');
            $table->time('delivery_notify_time')->nullable()->after('latest_delivery_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'latest_pickup_time',
                'pickup_notify_time',
                'latest_delivery_time',
                'delivery_notify_time'
            ]);
        });
    }
};
