<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->foreignId('aid_disaster_id')->nullable()->constrained('aid_disasters')->nullOnDelete();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('aid_disaster_id')->references('id')->on('aid_disasters')->onDelete('set null');
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('set null');
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
