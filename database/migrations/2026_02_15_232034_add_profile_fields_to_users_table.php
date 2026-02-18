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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->onDelete('set null');
            $table->string('website')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['sector_id']);
            $table->dropColumn(['country_id', 'phone', 'address', 'sector_id', 'website']);
        });
    }
};
