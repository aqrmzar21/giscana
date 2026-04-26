<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah relasi FK evacuation_facilities -> aid_disasters
     * dan kolom district_name (denormalized dari aid_disasters.district_name).
     */
    public function up(): void
    {
        Schema::table('evacuation_facilities', function (Blueprint $table) {
            $table->foreignId('aid_disaster_id')
                ->nullable()
                ->after('id')
                ->constrained('aid_disasters')
                ->nullOnDelete();
            $table->string('district_name')->nullable()->after('aid_disaster_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evacuation_facilities', function (Blueprint $table) {
            $table->dropForeign(['aid_disaster_id']);
            $table->dropColumn(['aid_disaster_id', 'district_name']);
        });
    }
};
