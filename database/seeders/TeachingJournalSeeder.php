<?php

namespace Database\Seeders;

use App\Models\TeachingJournal;
use App\Models\TeachingSchedule;
use Illuminate\Database\Seeder;

class TeachingJournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua ID teaching_schedule yang tersedia
        $scheduleIds = TeachingSchedule::pluck('id')->toArray();

        if (empty($scheduleIds)) {
            $this->command->warn('Tidak ada data teaching_schedule. Jalankan seeder TeachingScheduleSeeder terlebih dahulu.');
            return;
        }

        // Buat 20 data jurnal mengajar dummy
        for ($i = 0; $i < 20; $i++) {
            TeachingJournal::create([
                'teaching_schedule_id' => $scheduleIds[array_rand($scheduleIds)],
                'date'                => now()->subDays(rand(1, 60))->format('Y-m-d'),
                'material'            => 'Materi pertemuan ke-' . ($i + 1) . ': ' . fake()->sentence(4),
                'reflection'          => fake()->optional()->paragraph(2),
            ]);
        }

        $this->command->info('TeachingJournalSeeder selesai dijalankan.');
    }
}
