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
        // 1. Ambil atau Buat Role (Pastikan guard_name sesuai)
        $teacherRole = Role::where('name', 'teacher')->first()
            ?? Role::create(['name' => 'teacher', 'guard_name' => 'api']);

        // 2. Buat User Teacher dengan role_id (sesuai skema table users Anda)
        $teacher = User::create([
            'name'     => 'Budi Pratama, S.Pd',
            'email'    => 'teacher@sekolah1.com',
            'password' => Hash::make('password123'),
            'role_id'  => $teacherRole->id, // Mengisi field role_id di tabel users
        ]);

        // Berikan role secara Spatie (mengisi tabel model_has_roles)
        $teacher->assignRole($teacherRole);

        // 2. Buat Mata Pelajaran
        $subject1 = Subject::create(['name' => 'Bahasa Indonesia']);
        $subject2 = Subject::create(['name' => 'Matematika']);

        // 3. Buat Kelas
        $class1 = Classroom::create(['name' => 'Kelas X - MIPA 1']);
        $class2 = Classroom::create(['name' => 'Kelas XI - IPS 2']);

        // 4. Buat Siswa untuk Kelas X - MIPA 1
        $students = [
            ['name' => 'Aditya Pratama', 'nis' => '21001'],
            ['name' => 'Bunga Citra Lestari', 'nis' => '21002'],
            ['name' => 'Dimas Anggara', 'nis' => '21003'],
            ['name' => 'Eka Wijaya', 'nis' => '21004'],
            ['name' => 'Fahri Ramadhan', 'nis' => '21005'],
        ];

        foreach ($students as $student) {
            Student::create([
                'classroom_id' => $class1->id,
                'name' => $student['name'],
                'nis' => $student['nis'],
            ]);
        }

        // 5. Buat Jadwal Mengajar Guru Hari Ini (Sesuai Gambar 1)
        $today = now()->format('l'); // Mendapatkan nama hari ini (e.g., Monday)

        // Jadwal Sesi 1
        TeachingSchedule::create([
            'user_id' => $teacher->id,
            'subject_id' => $subject1->id,
            'classroom_id' => $class1->id,
            'day' => $today,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);

        // Jadwal Sesi 2
        TeachingSchedule::create([
            'user_id' => $teacher->id,
            'subject_id' => $subject2->id,
            'classroom_id' => $class2->id,
            'day' => $today,
            'start_time' => '10:00:00',
            'end_time' => '11:30:00',
        ]);
    }
}
