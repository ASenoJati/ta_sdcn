<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\LessonHour;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use App\Models\TeachingJournal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeachingJournalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Inisialisasi Role
        $teacherRole = Role::where('name', 'teacher')->first()
            ?? Role::create(['name' => 'teacher', 'guard_name' => 'web']);

        // 2. Data Guru & Mata Pelajaran
        $teachersData = [
            [
                'name' => 'Fawwaz Labib',
                'email' => 'fawwazlabib29@gmail.com',
                'subject' => 'Bahasa Indonesia'
            ],
            [
                'name' => 'Abyu Pandega',
                'email' => 'abyupandega@gmail.com',
                'subject' => 'Matematika'
            ],
            [
                'name' => 'Seno Jati',
                'email' => 'senojati16@gmail.com',
                'subject' => 'Bahasa Inggris'
            ],
        ];

        // 3. Hari
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // 4. Kelas
        $class1 = Classroom::firstOrCreate(['name' => 'Kelas X - MIPA 1']);
        $class2 = Classroom::firstOrCreate(['name' => 'Kelas XI - IPS 2']);

        // 5. Buat Jam Pelajaran
        $lessonHour1 = LessonHour::firstOrCreate(
            ['session' => 1],
            ['start_time' => '07:30:00', 'end_time' => '09:00:00']
        );
        $lessonHour2 = LessonHour::firstOrCreate(
            ['session' => 2],
            ['start_time' => '09:30:00', 'end_time' => '11:00:00']
        );

        $lessonHours = [$lessonHour1, $lessonHour2];
        $classrooms = [$class1, $class2];

        // 6. Loop Guru dan buat jadwal
        foreach ($teachersData as $index => $data) {
            $teacher = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $teacherRole->id,
                ]
            );
            $teacher->assignRole($teacherRole);

            $subject = Subject::firstOrCreate(['name' => $data['subject']]);

            foreach ($days as $day) {
                $lessonHour = $lessonHours[$index % count($lessonHours)];
                $classroom = $classrooms[$index % count($classrooms)];

                // Buat jadwal dengan cek unique
                TeachingSchedule::firstOrCreate([
                    'classroom_id'   => $classroom->id,
                    'day'            => $day,
                    'lesson_hour_id' => $lessonHour->id,
                ], [
                    'user_id'      => $teacher->id,
                    'subject_id'   => $subject->id,
                ]);
            }
        }

        // 7. Seed Siswa
        $this->seedStudents($class1->id);

        // 8. Seed Jurnal Mengajar (pastikan hanya untuk jadwal yang ada)
        $this->seedJournals();
    }

    private function seedStudents($classId): void
    {
        $students = [
            ['name' => 'Aditya Pratama', 'nis' => '21001'],
            ['name' => 'Bunga Citra Lestari', 'nis' => '21002'],
            ['name' => 'Dimas Anggara', 'nis' => '21003'],
        ];

        foreach ($students as $s) {
            Student::firstOrCreate(
                ['nis' => $s['nis']],
                ['name' => $s['name'], 'classroom_id' => $classId]
            );
        }
    }

    private function seedJournals(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $scheduleIds = TeachingSchedule::pluck('id')->toArray();

        if (empty($scheduleIds)) {
            $this->command->warn('Tidak ada data teaching_schedule. Jurnal tidak dibuat.');
            return;
        }

        // Ambil beberapa jadwal secara acak untuk dibuat jurnal
        $selectedSchedules = collect($scheduleIds)->random(min(10, count($scheduleIds)));

        foreach ($selectedSchedules as $scheduleId) {
            // Cek apakah sudah ada jurnal untuk tanggal yang sama (hindari duplikat)
            $date = $faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d');
            $existing = TeachingJournal::where('teaching_schedule_id', $scheduleId)
                ->whereDate('date', $date)
                ->exists();

            if (!$existing) {
                TeachingJournal::create([
                    'teaching_schedule_id' => $scheduleId,
                    'date'                 => $date,
                    'material'             => 'Materi: ' . $faker->sentence(6),
                    'reflection'           => $faker->optional(0.7)->paragraph(2),
                ]);
            }
        }

        $this->command->info('Berhasil menambahkan jurnal mengajar.');
    }
}
