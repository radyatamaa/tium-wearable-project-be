<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserAdminHospital;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAdminHospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_admin_hospitals')->insert([
            [
                'user_id' => '001',
                'password' => Hash::make('secret'),
                'name_person_in_charge' => 'Admin',
                'permission' => 'Admin',
                'registration_date' => '2024-07-15',
                'contact_person_position' => 'Manager',
                'contact_number' => '1234567890',
                'email' => 'john@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}