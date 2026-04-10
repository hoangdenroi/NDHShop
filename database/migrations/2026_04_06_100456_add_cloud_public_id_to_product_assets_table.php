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
        Schema::table('product_assets', function (Blueprint $table) {
            $table->string('cloud_public_id')->nullable()->after('url_or_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_assets', function (Blueprint $table) {
            $table->dropColumn('cloud_public_id');
        });
    }
};
