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
        Schema::table('evacuation_routes', function (Blueprint $table) {
            $table->enum('disaster_type', ['longsor', 'banjir', 'other'])->after('description')->default('other');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evacuation_routes', function (Blueprint $table) {
            $table->dropColumn('disaster_type');
        });
    }
};
