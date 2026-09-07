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
        Schema::create('disaster_hazard_layers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('disaster_type'); // banjir, gempa, gelombang, longsor
            $table->string('label');         // Nama tampilan: "Rawan Banjir"
            $table->string('geojson_path');  // Path relatif di public: /geojson/rawan_banjir.geojson
            $table->string('fill_color', 20)->default('#3b82f6');
            $table->string('border_color', 20)->default('#1d4ed8');
            $table->decimal('fill_opacity', 3, 2)->default(0.35);
            $table->decimal('border_weight', 4, 1)->default(1.5);
            $table->string('icon_emoji', 10)->nullable(); // ikon emoji untuk UI
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disaster_hazard_layers');
    }
};
