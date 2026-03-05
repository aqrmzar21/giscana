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
        Schema::create('disaster_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('disaster_type', ['longsor', 'banjir', 'other']);
            $table->text('description')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical']);
            $table->json('point_coordinates'); // Store GeoJSON point coordinates
            $table->decimal('area_hectares', 10, 2)->nullable();
            $table->integer('affected_population')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disaster_zones');
    }
};
