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
                'name'        => 'Kantor Pusat SD Cahaya Nur',
                'longitude'   => 110.370529, // Contoh koordinat (Yogyakarta)
                'latitude'    => -7.797068,
                'radius_km'   => 1, // Radius 1 KM
                'default'     => true,
                'address'     => 'Jl. Contoh No. 123, Kota Yogyakarta',
                'description' => 'Lokasi utama untuk presensi guru dan staff.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Gedung Olahraga (GOR)',
                'longitude'   => 110.375000,
                'latitude'    => -7.799000,
                'radius_km'   => 2,
                'default'     => false,
                'address'     => 'Jl. Olahraga No. 45, Kota Yogyakarta',
                'description' => 'Lokasi cadangan untuk kegiatan luar ruangan.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($locations as $location) {
            DB::table('locations')->updateOrInsert(
                ['name' => $location['name']], // Kunci unik untuk pengecekan
                $location
            );
        }
    }
}
