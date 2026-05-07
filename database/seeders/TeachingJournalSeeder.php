<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeachingJournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Inisialisasi Role
        $teacherRole = Role::where('name', 'teacher')->first()
            ?? Role::create(['name' => 'teacher', 'guard_name' => 'web']);

        // 2. Data Guru & Mata Pelajaran Utama
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

        // 3. Daftar Hari
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // 4. Inisialisasi Kelas
        $class1 = Classroom::firstOrCreate(['name' => 'Kelas X - MIPA 1']);
        $class2 = Classroom::firstOrCreate(['name' => 'Kelas XI - IPS 2']);

        // 5. Loop Utama: Guru -> Hari -> Sesi
        foreach ($teachersData as $data) {
            // Buat User Guru
            $teacher = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password123'),
                    'role_id'  => $teacherRole->id,
                ]
            );

            $teacher->assignRole($teacherRole);

            // Buat Mata Pelajaran
            $subject = Subject::firstOrCreate(['name' => $data['subject']]);

            foreach ($days as $day) {
                // Pengaturan Jam Khusus Jumat
                $session1_start = '07:30:00';
                $session1_end   = ($day === 'Friday') ? '08:30:00' : '09:00:00';

                $session2_start = ($day === 'Friday') ? '09:00:00' : '09:30:00';
                $session2_end   = ($day === 'Friday') ? '10:30:00' : '11:00:00';

                // Buat Jadwal Sesi 1 (Di Kelas 1)
                TeachingSchedule::create([
                    'user_id'      => $teacher->id,
                    'subject_id'   => $subject->id,
                    'classroom_id' => $class1->id,
                    'day'          => $day,
                    'start_time'   => $session1_start,
                    'end_time'     => $session1_end,
                ]);

                // Buat Jadwal Sesi 2 (Di Kelas 2)
                TeachingSchedule::create([
                    'user_id'      => $teacher->id,
                    'subject_id'   => $subject->id,
                    'classroom_id' => $class2->id,
                    'day'          => $day,
                    'start_time'   => $session2_start,
                    'end_time'     => $session2_end,
                ]);
            }
        }

        // 6. Buat Siswa Sample (Opsional)
        $this->seedStudents($class1->id);
    }

    private function seedStudents($classId)
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
}
