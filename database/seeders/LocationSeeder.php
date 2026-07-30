<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name'        => 'SD Cahaya Nur',
                'longitude'   => 110.84460079072929,
                'latitude'    => -6.807101770145372,
                'radius_km'   => 1,
                'default'     => true,
                'address'     => 'Jl. Contoh No. 123, Kota Yogyakarta',
                'description' => 'Lokasi default untuk pengembangan aplikasi',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'SMK Raden Umar Said',
                'longitude'   => 110.8428394438202,
                'latitude'    => -6.753778591552996,
                'radius_km'   => 2,
                'default'     => false,
                'address'     => 'Jalan Sukun Raya No.09, Besito Kulon, Besito, Kec. Gebog, Kabupaten Kudus, Jawa Tengah 59333',
                'description' => 'Lokasi development',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($locations as $location) {
            DB::table('locations')->updateOrInsert(
                ['name' => $location['name']],
                $location
            );
        }
    }
}
