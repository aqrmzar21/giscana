<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aid_distributions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('beneficiary_id')->constrained('aid_beneficiaries')->onDelete('cascade');
            $table->foreignId('aid_inventory_id')->constrained('aid_inventories')->onDelete('cascade');
            $table->foreignId('village_id')->constrained('villages')->onDelete('cascade');
            $table->foreignId('aid_disaster_id')->nullable()->constrained('aid_disasters')->onDelete('set null');
            $table->integer('quantity_received');           // Jumlah Diterima
            $table->date('distribution_date');              // Tanggal Penyaluran
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aid_distributions');
    }
};