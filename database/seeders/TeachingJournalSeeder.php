<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\LessonHour;
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
            ?? Role::create([
                'name' => 'teacher',
                'guard_name' => 'web'
            ]);

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
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];

        // 4. Kelas
        $class1 = Classroom::firstOrCreate([
            'name' => 'Kelas X - MIPA 1'
        ]);

        $class2 = Classroom::firstOrCreate([
            'name' => 'Kelas XI - IPS 2'
        ]);

        /**
         * 5. Buat Jam Pelajaran
         */
        $lessonHour1 = LessonHour::firstOrCreate(
            ['session' => 1],
            [
                'start_time' => '07:30:00',
                'end_time'   => '09:00:00',
            ]
        );

        $lessonHour2 = LessonHour::firstOrCreate(
            ['session' => 2],
            [
                'start_time' => '09:30:00',
                'end_time'   => '11:00:00',
            ]
        );

        /**
         * 6. Loop Guru
         */
        $lessonHours = LessonHour::all();
        $classrooms = [$class1, $class2];

        foreach ($teachersData as $teacherIndex => $data) {

            $teacher = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $teacherRole->id,
                ]
            );

            $teacher->assignRole($teacherRole);

            $subject = Subject::firstOrCreate([
                'name' => $data['subject']
            ]);

            foreach ($days as $dayIndex => $day) {

                $lessonHour = $lessonHours[$teacherIndex % $lessonHours->count()];
                $classroom  = $classrooms[$teacherIndex % count($classrooms)];

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
                [
                    'name' => $s['name'],
                    'classroom_id' => $classId
                ]
            );
        }
    }
}
