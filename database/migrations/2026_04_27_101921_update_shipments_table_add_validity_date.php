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
            $table->datetime('validity_date')->nullable()->after('latest_delivery_time');
            $table->dropColumn(['pickup_type', 'delivery_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('validity_date');
            $table->string('pickup_type')->nullable();
            $table->string('delivery_type')->nullable();
        });
    }
};
