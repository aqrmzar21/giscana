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
        Schema::create('evacuation_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('disaster_type', ['longsor', 'banjir', 'other']);
            $table->json('line_coordinates'); // Store GeoJSON linestring coordinates
            $table->decimal('length_km', 8, 2)->nullable();
            $table->enum('route_type', ['primary', 'secondary', 'emergency']);
            $table->boolean('is_accessible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evacuation_routes');
    }
};
