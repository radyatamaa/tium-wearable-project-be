<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserManagementGeneralPurpose;

class UserManagementReportGeneralPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate additional dummy data
        for ($i = 1; $i <= 10; $i++) {
            UserManagementGeneralPurpose::create([
                'name' => "User $i",
                'gender' => $i % 2 == 0 ? 'Man' : 'Woman',
                'age' => rand(18, 60),
                'affiliations' => 'Affiliation ' . $i,
            ]);
        }
    }
}