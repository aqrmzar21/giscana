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
            $table->unsignedBigInteger('village_id')->nullable();
            // $table->unsignedBigInteger('district_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

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
