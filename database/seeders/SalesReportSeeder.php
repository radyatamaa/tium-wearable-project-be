<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalesReport;
use Faker\Factory as Faker;

class SalesReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function generateRandomString($length = 10) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            SalesReport::create([
                'user_id' => $this->generateRandomString(10),
                'organization_name' => $faker->company,
                'address' => $faker->address,
                'person_in_charge' => $faker->name,
                'transaction_id' => strtoupper($faker->bothify('????-####')),
                'service_used' => $faker->word,
                'total_amount' => $faker->randomFloat(2, 1000, 10000),
                'payment_method' => $faker->randomElement(['Credit Card', 'Bank Transfer', 'Virtual Account', 'Real-Time Account Transfer']),
                'payment_date' => $faker->date(),
            ]);
        }
    }
}