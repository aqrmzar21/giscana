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
    }
};
