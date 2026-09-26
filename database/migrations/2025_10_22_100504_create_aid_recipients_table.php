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
        Schema::create('aid_recipients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->date('date');
            $table->string('aid_type');
            $table->integer('amount');
            $table->string('recipient_name');
            // Foreign key otomatis dibuat oleh ->constrained()
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->foreignId('aid_disaster_id')->nullable()->constrained('aid_disasters')->nullOnDelete();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Baris $table->foreign(...) di bagian bawah HAPUS karena duplikat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aid_recipients');
    }
};