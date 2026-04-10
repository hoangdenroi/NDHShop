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
        Schema::create('settings', function (Blueprint $table) {
            $table->baseColumns();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('key');
            $table->json('value')->nullable();
            $table->string('description')->nullable();

            $table->unique(['user_id', 'key']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
