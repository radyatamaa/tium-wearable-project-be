<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        $adminLevels = [
            '일반관리자', // General Administrator
            '중간 관리자', // middle administrator
            '최종 관리자' // Final Administrator
        ];
        DB::table('users')->insert(
            [
            'id' => 1,
            'name' => 'Admin',
            'user_id' => '001',
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
            'status_of_usage_inquiries_unconfirmed' => 10,
            'status_of_usage_inquiries_receiving' => 5,
            'status_of_usage_inquiries_answer_completed' => 3,
            'registration_date' => Carbon::now(),
            'last_login' => Carbon::now(),
            'administrator_level' => '최종 관리자',
            'position' => 'CEO',
            'phone_number' => '1234567890',
            'email' => 'admin1@example.com'
        ]
    );
    }
}