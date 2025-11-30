<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserManagementPublicWelfare;

class UserManagementReportPublicWelfareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            UserManagementPublicWelfare::create([
                'name' => "User $i",
                'gender' => $i % 2 == 0 ? 'Man' : 'Woman',
                'age' => rand(18, 60),
                'affiliations' => 'Affiliation ' . $i,
            ]);
        }
    }
}