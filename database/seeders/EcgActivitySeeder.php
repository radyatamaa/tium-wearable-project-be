<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EcgActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EcgActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userCustomerIds = DB::table('user_customers')->pluck('id');

        foreach ($userCustomerIds as $userId) {
            for ($i = 0; $i < 50; $i++) { // Generate 50 dummy records for each user
                $recordedAt = Carbon::now()->subDays(rand(0, 30))->subMinutes(rand(0, 1440));

                EcgActivity::create([
                    'user_customer_id' => $userId,
                    'ecg_data' => json_encode($this->generateEcgData()),
                    'device_address' => Str::random(12),
                    'recorded_at' => $recordedAt,
                    'activity_type' => $this->getRandomActivityType(),
                    // 'cardiac_health_index' => rand(50, 100) / 10, // Random value between 5.0 and 10.0
                ]);
            }
        }
    }

    private function generateEcgData()
    {
        $ecgData = [];
        for ($i = 0; $i < 100; $i++) { // Generate 100 ECG readings
            $ecgData[] = rand(500, 1500) / 100; // Random value between 5.0 and 15.0
        }
        return $ecgData;
    }

    private function getRandomActivityType()
    {
        $types = ['resting', 'walking', 'exercise', 'sleep'];
        return $types[array_rand($types)];
    }
}