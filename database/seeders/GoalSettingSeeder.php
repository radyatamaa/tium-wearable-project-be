<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GoalSetting;
use Illuminate\Support\Facades\DB;

class GoalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate the table before seeding
        DB::table('goal_settings')->truncate();

        $userIds = DB::table('user_customers')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            GoalSetting::create([
                'user_customer_id' => $userId,
                'exercise_goal' => 7000, // Default value
                'sleep_goal' => 8, // Default value
            ]);
        }
    }
}