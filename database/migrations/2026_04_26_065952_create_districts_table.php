<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();          // kode kecamatan (LCODE)
            $table->string('name', 100);                     // nama kecamatan (NAMOBJ)
            $table->string('regency', 100)->nullable();      // kabupaten/kota (WADMKK)
            $table->string('province', 100)->nullable();     // provinsi (WADMPR)
            $table->string('remark', 255)->nullable();       // REMARK (opsional)
            $table->geometry('geom');
            $table->timestamps();

            $table->spatialIndex('geom');
        });
    }

    public function down()
    {
        Schema::dropIfExists('districts');
    }
};