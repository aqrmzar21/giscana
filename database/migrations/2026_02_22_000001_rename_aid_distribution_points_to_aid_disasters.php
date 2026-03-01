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
        // Drop tabel lama
        Schema::dropIfExists('aid_distribution_points');

        // Buat tabel baru aid_disasters
        Schema::create('aid_disasters', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kecamatan');
            $table->integer('jumlah_penerima_bantuan')->nullable();
            $table->integer('bantuan_terdistribusi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aid_disasters');

        Schema::create('aid_distribution_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('aid_type', ['food', 'medical', 'clothing', 'shelter', 'mixed']);
            $table->json('point_coordinates');
            $table->string('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->integer('capacity_per_day')->nullable();
            $table->boolean('is_accessible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
