<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAttendanceTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari masing-masing tabel untuk memastikan akurasi data
        $roles = DB::table('roles')->pluck('id', 'name');
        $settings = DB::table('attendance_time_settings')->pluck('id', 'name');

        $roleSettings = [];

        // Contoh mapping: Menghubungkan Role 'Staff' ke 'Shift Pagi'
        if (isset($roles['teacher']) && isset($settings['Shift Pagi (Regular)'])) {
            $roleSettings[] = [
                'role_id'                      => $roles['teacher'],
                'attendance_time_settings_id'  => $settings['Shift Pagi (Regular)'],
                'created_at'                   => now(),
                'updated_at'                   => now(),
            ];
        }

        // Contoh mapping: Menghubungkan Role 'Security' ke 'Shift Sore'
        if (isset($roles['staff']) && isset($settings['Shift Sore'])) {
            $roleSettings[] = [
                'role_id'                      => $roles['staff'],
                'attendance_time_settings_id'  => $settings['Shift Sore'],
                'created_at'                   => now(),
                'updated_at'                   => now(),
            ];
        }

        // Jika data mapping tersedia, masukkan ke database
        if (!empty($roleSettings)) {
            DB::table('role_attendance_times')->insert($roleSettings);
        }
    }
}
