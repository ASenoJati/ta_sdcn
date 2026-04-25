<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Role ke tabel roles (Spatie)
        $adminRole   = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $teacherRole = Role::create(['name' => 'teacher', 'guard_name' => 'api']);
        $staffRole   = Role::create(['name' => 'staff', 'guard_name' => 'api']);

        // 2. Buat User Admin
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@sekolah.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole($adminRole);

        // 3. Buat User Teacher
        $teacher = User::create([
            'name'     => 'Budi Guru',
            'email'    => 'teacher@sekolah.com',
            'password' => Hash::make('password123'),
        ]);
        $teacher->assignRole($teacherRole);

        // 4. Buat User Staff
        $staff = User::create([
            'name'     => 'Siti Staff',
            'email'    => 'staff@sekolah.com',
            'password' => Hash::make('password123'),
        ]);
        $staff->assignRole($staffRole);

        $this->command->info('Seed User dan Role berhasil dibuat!');
    }
}
