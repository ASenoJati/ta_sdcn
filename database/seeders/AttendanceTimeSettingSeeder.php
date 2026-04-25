<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceTimeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'name'                 => 'Shift Pagi (Regular)',
                'check_in_start'       => '07:00:00',
                'check_in_end'         => '09:00:00',
                'check_out_start'      => '17:00:00',
                'check_out_end'        => '19:00:00',
                'grace_period_minutes' => 15,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'name'                 => 'Shift Sore',
                'check_in_start'       => '14:00:00',
                'check_in_end'         => '15:30:00',
                'check_out_start'      => '22:00:00',
                'check_out_end'        => '23:59:00',
                'grace_period_minutes' => 10,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'name'                 => 'Full Day / Overtime',
                'check_in_start'       => '08:00:00',
                'check_in_end'         => '10:00:00',
                'check_out_start'      => '20:00:00',
                'check_out_end'        => '22:00:00',
                'grace_period_minutes' => 0,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ];

        DB::table('attendance_time_settings')->insert($settings);
    }
}
