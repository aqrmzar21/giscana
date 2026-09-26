<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aid_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            // $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('cascade');
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->foreignId('village_id')->constrained('villages')->onDelete('cascade');
            $table->string('recipient_name');               // Nama Kepala Keluarga / Penerima
            $table->string('identity_card_number', 16)->nullable()->unique(); // NIK
            $table->text('address_detail')->nullable();
            $table->enum('aid_status', ['pending', 'received'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};