<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AidRecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\AidRecipient::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $villages = \App\Models\Village::with('district')->get();

        if ($villages->isEmpty()) {
            return;
        }

        $faker = \Faker\Factory::create('id_ID');
        $aidTypes = ['Sembako', 'Material Bangunan', 'Pakaian Layak Pakai', 'Obat-obatan'];

        foreach (range(1, 30) as $i) {
            $village = $villages->random();
            
            \App\Models\AidRecipient::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'date' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'aid_type' => $faker->randomElement($aidTypes),
                'amount' => $faker->numberBetween(1, 50),
                'recipient_name' => $faker->name,
                'village_id' => $village->id,
                'name' => $village->district->name,
                'description' => 'Bantuan disalurkan untuk korban bencana terdampak',
            ]);
        }
    }
}
