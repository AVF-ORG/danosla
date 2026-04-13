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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_subject_id')->constrained('contact_subjects')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('content');
            $table->text('reply_content')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
