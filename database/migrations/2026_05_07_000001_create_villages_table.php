<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');           // foreign key ke tabel districts
            $table->string('code', 30)->nullable();             // kode desa (kode_kd)
            $table->string('yard', 100);                        // nama desa (kel_desa)
            $table->string('full_name', 150)->nullable();       // nama lengkap (nama)
            $table->string('zone', 100);                        // nama kec (kecamatan)
            $table->string('regency', 100)->nullable();         // kabupaten/kota (kab_kota)
            $table->string('province', 100)->nullable();        // provinsi
            $table->geometry('geom');                           // geometri wilayah desa
            $table->timestamps();

            $table->foreign('district_id')
                  ->references('id')
                  ->on('districts')
                  ->onDelete('cascade');

            $table->spatialIndex('geom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
