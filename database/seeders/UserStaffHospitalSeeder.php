<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class UserStaffHospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $jobTitles = [
            '의사', // Doctor
            '간호사', // Nurse
            '기술자', // Technician
        ];

        $departments = [
            '진단의학과', // Diagnostic Medicine
            '외과', // Surgery
            '내과', // Internal Medicine
        ];

        $genders = ['남성', '여성']; // Male, Female

        $staffMembers = [
            [
                'name' => '홍길동',
                'age' => 46,
                'job_title' => '의사',
                'gender' => '남성',
                'department' => '진단의학과',
                'address' => '서울시 강남구',
                'position' => '과장',
                'contact1' => '01012345678',
                'employee_number' => 'AB123456789',
                'contact2' => '01098765432',
                'birthdate' => '1975-01-01',
                'email' => 'honggildong@example.com',
                'user_id' => '001',
                'password' => Hash::make('secret'),
                'photo_profile' => 'default-avatar.png',
                'registration_date' => Carbon::now()->format('Y-m-d'),
            ],
        ];

        for ($i = 0; $i < 1; $i++) {
            $staffMembers[] = [
                'name' => $faker->name,
                'age' => $faker->numberBetween(25, 65),
                'job_title' => $faker->randomElement($jobTitles),
                'gender' => $faker->randomElement($genders),
                'department' => $faker->randomElement($departments),
                'address' => $faker->address,
                'position' => $faker->jobTitle,
                'contact1' => $faker->phoneNumber,
                'employee_number' => $faker->unique()->regexify('[A-Z0-9]{10}'),
                'contact2' => $faker->optional()->phoneNumber,
                'birthdate' => $faker->date('Y-m-d'),
                'email' => $faker->unique()->safeEmail,
                'user_id' => $faker->unique()->userName,
                'password' => Hash::make('password'),
                'photo_profile' => 'default-avatar.png',
                'registration_date' => Carbon::now()->format('Y-m-d'),
            ];
        }

        DB::table('user_staff_hospitals')->insert($staffMembers);
    }
}