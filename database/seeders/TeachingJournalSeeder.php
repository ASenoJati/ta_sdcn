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
        // 1. Ambil atau Buat Role
        $teacherRole = Role::where('name', 'teacher')->first()
            ?? Role::create(['name' => 'teacher', 'guard_name' => 'api']);

        // 2. Data Guru
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

        // 3. Buat Kelas (Contoh)
        $class1 = Classroom::firstOrCreate(['name' => 'Kelas X - MIPA 1']);
        $class2 = Classroom::firstOrCreate(['name' => 'Kelas XI - IPS 2']);

        $today = now()->format('l');

        // 4. Proses Pembuatan User, Role, dan Jadwal
        foreach ($teachersData as $index => $data) {
            // Create Teacher
            $teacher = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password123'),
                'role_id'  => $teacherRole->id,
            ]);

            $teacher->assignRole($teacherRole);

            // Create Subject
            $subject = Subject::firstOrCreate(['name' => $data['subject']]);

            // 5. Buat Jadwal Mengajar (Setiap guru punya 2 sesi di hari yang sama)
            // Sesi 1
            TeachingSchedule::create([
                'user_id'      => $teacher->id,
                'subject_id'   => $subject->id,
                'classroom_id' => $class1->id,
                'day'          => $today,
                'start_time'   => '07:30:00',
                'end_time'     => '09:00:00',
            ]);

            // Sesi 2
            TeachingSchedule::create([
                'user_id'      => $teacher->id,
                'subject_id'   => $subject->id,
                'classroom_id' => $class2->id,
                'day'          => $today,
                'start_time'   => '09:30:00',
                'end_time'     => '11:00:00',
            ]);
        }

        // 6. Buat Siswa Sample untuk Kelas 1 (Hanya sekali)
        $students = [
            ['name' => 'Aditya Pratama', 'nis' => '21001'],
            ['name' => 'Bunga Citra Lestari', 'nis' => '21002'],
            ['name' => 'Dimas Anggara', 'nis' => '21003'],
        ];

        foreach ($students as $s) {
            Student::firstOrCreate(
                ['nis' => $s['nis']],
                ['name' => $s['name'], 'classroom_id' => $class1->id]
            );
        }
    }
}
