<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DistrictSeeder::class,
            VillageSeeder::class,
            GiscanaDataSeeder::class,
            AidRecipientSeeder::class,
        ]);
    }
}

