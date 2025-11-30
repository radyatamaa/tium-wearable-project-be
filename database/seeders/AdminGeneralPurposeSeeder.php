<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminGeneralPurpose;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminGeneralPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminLevels = [
            '일반관리자', // General Administrator
            '중간 관리자', // middle administrator
            '최종 관리자' // Final Administrator
        ];
        AdminGeneralPurpose::create([
            'user_id' => '011',
            'password' => Hash::make('secret'),
            'name' => 'Admin General Purpose',
            'classification' => '최종 관리자',
            'department' => 'IT',
            'contact' => 'admin@example.com',
            'registration_date' => Carbon::now(),
        ]);
    }
}