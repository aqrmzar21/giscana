<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Relasi: evacuation_routes -> evacuation_facilities (rute menuju fasilitas).
     * nama_fasilitas = denormalized dari evacuation_facilities.name.
     */
    public function up(): void
    {
        Schema::table('evacuation_routes', function (Blueprint $table) {
            $table->foreignId('evacuation_facility_id')
                ->nullable()
                ->after('id')
                ->constrained('evacuation_facilities')
                ->nullOnDelete();
            $table->string('nama_fasilitas')->nullable()->after('evacuation_facility_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evacuation_routes', function (Blueprint $table) {
            $table->dropForeign(['evacuation_facility_id']);
            $table->dropColumn(['evacuation_facility_id', 'nama_fasilitas']);
        });
    }
};
