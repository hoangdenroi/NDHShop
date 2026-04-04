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
        Schema::table('vps_categories', function (Blueprint $table) {
            $table->string('server_group', 50)->default('regular')->after('hetzner_server_type')->comment('Cost-Optimized, Regular Performance, General Purpose...');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_categories', function (Blueprint $table) {
            $table->dropColumn('server_group');
        });
    }
};
