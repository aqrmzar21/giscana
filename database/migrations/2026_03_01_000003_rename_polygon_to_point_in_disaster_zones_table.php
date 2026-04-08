<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengganti nama kolom polygon_coordinates -> point_coordinates
     * tanpa mengubah tipe data (masih JSON).
     */
    public function up(): void
    {
        Schema::table('disaster_zones', function (Blueprint $table) {
            if (Schema::hasColumn('disaster_zones', 'polygon_coordinates')) {
                $table->renameColumn('polygon_coordinates', 'point_coordinates');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disaster_zones', function (Blueprint $table) {
            if (Schema::hasColumn('disaster_zones', 'point_coordinates')) {
                $table->renameColumn('point_coordinates', 'polygon_coordinates');
            }
        });
    }
};

