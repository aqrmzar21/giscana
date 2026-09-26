<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aid_inventories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('item_name');                    // Nama Bantuan / Barang
            $table->enum('category', ['sembako', 'obat', 'pakaian', 'material', 'lainnya']);
            $table->string('source');                       // Sumber Donasi / BPBD
            $table->integer('initial_stock');               // Jumlah Masuk
            $table->integer('remaining_stock');             // Sisa Stok
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aid_inventories');
    }
};