<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    protected $tables = [
        'users',
        'disaster_zones',
        'evacuation_routes',
        'evacuation_facilities',
        'aid_disasters'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'uuid')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->unique()->after('id');
                });

                // Populate UUID for existing records
                $records = DB::table($tableName)->whereNull('uuid')->get();
                foreach ($records as $record) {
                    DB::table($tableName)->where('id', $record->id)->update([
                        'uuid' => (string) Str::uuid()
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
