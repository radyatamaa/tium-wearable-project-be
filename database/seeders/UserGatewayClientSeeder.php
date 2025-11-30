<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserCustomerGatewayClient;
use Illuminate\Support\Facades\DB;

class UserGatewayClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    function generate_secret_key($serial_number, $predefined_string) {
        // Concatenate the serial number with the predefined string
        $concatenated_string = $serial_number . $predefined_string;
    
        // Perform SHA-256 hashing
        $secret_key = hash('sha256', $concatenated_string);
    
        return $secret_key;
    }

    public function run(): void
    {
        DB::table('user_customer_gateway_clients')->truncate();
        $faker = \Faker\Factory::create();
        // Create 10 users
        for ($i = 1; $i <= 1; $i++) {
            $digit = "dlsgf5";
            $serialNumber = 'MC806599A50178';
            $secret_key = $this->generate_secret_key($serialNumber,"dlsgf5");
            $user = UserCustomerGatewayClient::create([
                'serial_number_device' => 'MC806599A50178',
                'secret_key' => $secret_key,
                'digit' => $digit,
            ]);
        }
    }
}