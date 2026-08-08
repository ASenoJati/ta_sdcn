<?php

namespace Database\Seeders;

use App\Models\TeachingJournal;
use App\Models\TeachingSchedule;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TeachingJournalEntrySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $scheduleIds = TeachingSchedule::pluck('id')->toArray();

        if (empty($scheduleIds)) {
            $this->command->warn('Tidak ada data teaching_schedule. Jalankan seeder TeachingJournalSeeder terlebih dahulu.');
            return;
        }

        $jumlahJurnal = 30;

        for ($i = 0; $i < $jumlahJurnal; $i++) {
            $scheduleId = $scheduleIds[array_rand($scheduleIds)];
            $date = $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d');

            // Cegah duplikasi (opsional)
            $exists = TeachingJournal::where('teaching_schedule_id', $scheduleId)
                ->whereDate('date', $date)
                ->exists();

            if (!$exists) {
                TeachingJournal::create([
                    'teaching_schedule_id' => $scheduleId,
                    'date'                 => $date,
                    'material'             => 'Materi: ' . $faker->sentence(6),
                    'reflection'           => $faker->optional(0.7)->paragraph(2),
                ]);
            }
        }

        $this->command->info("Berhasil menambahkan {$jumlahJurnal} jurnal mengajar.");
    }
}
