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
        Schema::create('notifications', function (Blueprint $table) {
            $table->baseColumns();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('scope', 20); // PERSONAL, BROADCAST, SYSTEM
            $table->string('title', 255);
            $table->text('message')->nullable();
            $table->string('type', 50)->nullable(); // PROMOTION, PAYMENT, ORDER, BALANCE, MAINTENANCE, FEATURE
            $table->string('related_entity_type', 50)->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('priority', 20)->nullable(); // LOW, MEDIUM, HIGH, URGENT
            $table->text('action_url')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
