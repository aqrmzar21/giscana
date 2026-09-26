<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AidDistribution;
use App\Models\AidBeneficiary;
use App\Models\AidInventory;
use App\Models\AidDisaster;
use App\Models\User;
use App\Models\District; // Tambahkan ini
use App\Models\Village;  // Tambahkan ini
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AidDisasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AidInventory::truncate();
        Schema::enableForeignKeyConstraints();

        $inventories = [
            [
                'item_name'       => 'Paket Sembako Bencana',
                'category'        => 'sembako',
                'source'          => 'BPBD Provinsi Gorontalo',
                'initial_stock'   => 2000,
                'remaining_stock' => 2000,
                'is_active'       => true,
            ],
            [
                'item_name'       => 'Obat-obatan & Kit Medis Pertolongan Pertama',
                'category'        => 'obat',
                'source'          => 'Dinkes Bone Bolango',
                'initial_stock'   => 800,
                'remaining_stock' => 800,
                'is_active'       => true,
            ],
            [
                'item_name'       => 'Selimut & Pakaian Layak Pakai',
                'category'        => 'pakaian',
                'source'          => 'Donasi Tagana',
                'initial_stock'   => 1200,
                'remaining_stock' => 1200,
                'is_active'       => true,
            ],
            [
                'item_name'       => 'Tenda Pengungsian & Terpal',
                'category'        => 'lainnya',
                'source'          => 'BNPB Pusat',
                'initial_stock'   => 500,
                'remaining_stock' => 500,
                'is_active'       => true,
            ],
            [
                'item_name'       => 'Paket Family Hygiene Kit',
                'category'        => 'lainnya',
                'source'          => 'Dinas Sosial Bone Bolango',
                'initial_stock'   => 1000,
                'remaining_stock' => 1000,
                'is_active'       => true,
            ],
        ];

        foreach ($inventories as $inv) {
            AidInventory::create(array_merge($inv, [
                'uuid' => (string) Str::uuid(),
            ]));
        }
        
        // Penerima Bantuan (AidBeneficiary)
        Schema::disableForeignKeyConstraints();
        AidBeneficiary::truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Faker::create('id_ID');

        $targetDistricts = ['Bone Raya', 'Bulawa', 'Bone', 'Bonepantai', 'Kabila Bone'];
        $districts = District::whereIn('name', $targetDistricts)->get();

        if ($districts->isEmpty()) {
            $districts = District::all();
        }

        foreach ($districts as $district) {
            $villages = Village::where('district_id', $district->id)->get();

            // Jika desa belum ada, buatkan desa dummy otomatis
            if ($villages->isEmpty()) {
                $dummyVillage = Village::create([
                    'district_id' => $district->id,
                    'code'        => 'VIL' . rand(1000, 9999),
                    'yard'        => 'Desa Pusat ' . $district->name,
                    'full_name'   => 'Desa Pusat ' . $district->name,
                    'zone'        => $district->name,
                ]);
                $villages = collect([$dummyVillage]);
            }

            // Generate 10 data warga per desa
            foreach ($villages as $village) {
                for ($i = 0; $i < 10; $i++) {
                    AidBeneficiary::create([
                        'uuid'                 => (string) Str::uuid(),
                        'district_id'          => $district->id,
                        'village_id'           => $village->id,
                        'recipient_name'       => $faker->name(),
                        'identity_card_number' => $faker->unique()->nik(),
                        'address_detail'       => 'Dusun ' . $faker->numberBetween(1, 4) . ', ' . $village->full_name,
                        'aid_status'           => 'pending',
                    ]);
                }
            }
        }

        // Distribusi (AidDistribution)
        Schema::disableForeignKeyConstraints();
        AidDistribution::truncate();
        Schema::enableForeignKeyConstraints();

        $adminUser = User::first();
        $beneficiaries = AidBeneficiary::with(['district', 'village'])->get();
        $inventories = AidInventory::where('remaining_stock', '>', 0)->get();

        if ($beneficiaries->isEmpty() || $inventories->isEmpty()) {
            return;
        }

        // Ambil 40% warga secara acak untuk disimulasikan menerima bantuan
        $selectedBeneficiaries = $beneficiaries->random((int) ($beneficiaries->count() * 0.4));

        foreach ($selectedBeneficiaries as $beneficiary) {
            $inventory = $inventories->random();
            $quantity = $faker->numberBetween(1, 3);

            if ($inventory->remaining_stock >= $quantity) {
                DB::transaction(function () use ($beneficiary, $inventory, $quantity, $adminUser, $faker) {
                    // Pencarian fleksibel
                    $aidDisaster = AidDisaster::where('district_id', $beneficiary->district_id)
                        ->orWhere('district_name', $beneficiary->district?->name)
                        ->first();

                    AidDistribution::create([
                        'uuid'              => (string) Str::uuid(),
                        'beneficiary_id'    => $beneficiary->id,
                        'aid_inventory_id'  => $inventory->id,
                        'village_id'        => $beneficiary->village_id,
                        'aid_disaster_id'   => $aidDisaster?->id,
                        'quantity_received' => $quantity,
                        'distribution_date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                        'user_id'           => $adminUser?->id,
                        'description'       => 'Penyaluran bantuan logistik bencana secara langsung.',
                    ]);

                    // Potong stok barang logistik
                    $inventory->decrement('remaining_stock', $quantity);

                    // Update status penerima bantuan
                    $beneficiary->update(['aid_status' => 'received']);
                });
            }
        }

        // Hitung ulang akumulasi target & realisasi bantuan per kecamatan
        foreach (AidDisaster::all() as $aidDisaster) {
            $aidDisaster->recalculate();
        }
    }
}