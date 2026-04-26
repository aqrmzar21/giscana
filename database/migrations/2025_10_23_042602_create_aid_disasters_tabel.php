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
        // Create new table aid_disasters
        Schema::create('aid_disasters', function (Blueprint $table) {
            $table->id();
            $table->string('district_name'); // nama_kecamatan
            $table->integer('total_recipients')->nullable(); // jumlah_penerima_bantuan
            $table->integer('distributed_aid')->nullable(); // bantuan_terdistribusi
            $table->boolean('is_active')->default(true); // is_active
            $table->timestamp('last_synced_at')->nullable(); // last_synced_at
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
