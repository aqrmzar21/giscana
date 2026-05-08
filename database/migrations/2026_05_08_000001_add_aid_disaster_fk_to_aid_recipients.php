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
        Schema::table('aid_recipients', function (Blueprint $table) {
            $table->unsignedBigInteger('aid_disaster_id')
                ->nullable()
                ->after('village_id');
            $table->foreign('aid_disaster_id')
                ->references('id')
                ->on('aid_disasters')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aid_recipients', function (Blueprint $table) {
            $table->dropForeign(['aid_disaster_id']);
            $table->dropColumn('aid_disaster_id');
        });
    }
};
