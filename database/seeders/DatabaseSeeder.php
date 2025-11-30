<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // part of public health center site 
        $this->call([UsersTableSeeder::class]);
        $this->call([ServiceDetailedFieldSettingSeeder::class]);
        $this->call([ServiceJobSettingSeeder::class]);
        $this->call([ServiceUsageSettingSeeder::class]);

        // part of cms industrial site / general purpose
        // $this->call([AdminGeneralPurposeSeeder::class]);
        // $this->call([MeasurementSettingsGeneralPurposeSeeder::class]);

        // part of api mobile
        $this->call([UserCustomerSeeder::class]);
        // $this->call([BloodPressureSeeder::class]);
        // $this->call([BodyTemperatureSeeder::class]);
        // $this->call([HeartRateActivitySeederDummy::class]);
        // $this->call([MenstrualCycleSeeder::class]);
        // $this->call([OxygenSaturationSeeder::class]);
        // $this->call([RespiratoryRateSeeder::class]);
        // $this->call([SleepSeeder::class]);
        // $this->call([NotificationRangeSeeder::class]);
        // $this->call([GoalSettingSeeder::class]);
    }
}