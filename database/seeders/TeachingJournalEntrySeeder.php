<?php

namespace Database\Seeders;

use App\Models\TeachingJournal;
use App\Models\TeachingSchedule;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TeachingJournalEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua ID teaching_schedule yang tersedia
        $scheduleIds = TeachingSchedule::pluck('id')->toArray();

        if (empty($scheduleIds)) {
            $this->command->warn('Tidak ada data teaching_schedule. Jalankan seeder TeachingJournalSeeder terlebih dahulu.');
            return;
        }

        // Jumlah jurnal yang akan dibuat (misal 30)
        $jumlahJurnal = 30;

        for ($i = 0; $i < $jumlahJurnal; $i++) {
            TeachingJournal::create([
                'teaching_schedule_id' => $scheduleIds[array_rand($scheduleIds)],
                'date'                 => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'material'             => 'Materi: ' . $faker->sentence(6),
                'reflection'           => $faker->optional(0.7)->paragraph(2),
            ]);
        }

        $this->command->info("Berhasil menambahkan {$jumlahJurnal} jurnal mengajar.");
    }
}
