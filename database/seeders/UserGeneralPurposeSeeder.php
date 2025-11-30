<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserGeneralPurpose;
use App\Models\SubscriptionInformationGeneralPurpose;
use App\Models\PersonInChargeInformationGeneralPurpose;
use App\Models\AdditionalInformationGeneralPurpose;
use App\Models\ServiceSubscriptionInformationGeneralPurpose;
use Illuminate\Support\Facades\Hash;

class UserGeneralPurposeSeeder extends Seeder
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
        $faker = \Faker\Factory::create();
        // Create 10 users
        for ($i = 1; $i <= 1; $i++) {
            $user = UserGeneralPurpose::create([
                'user_id' => '001',
                'nickname' => 'User' . $i,
                'address' => 'Address' . $i,
                'receive_email' => $i % 2 == 0 ? 'Receive' : 'Not Receive',
                'receive_sms' => $i % 2 == 0 ? 'Receive' : 'Not Receive',
                'data_output' => 'Use',
                'member_status' => 'Active',
                'password' => Hash::make('secret'),
                'user_type' => $faker->randomElement(['MEDICAL_INSTITUTION','PUBLIC_WELFARE','INDUSTRIAL_SCENE'])
            ]);

            // Create related subscription information
            SubscriptionInformationGeneralPurpose::create([
                'user_general_purpose_id' => $user->id,
                'membership_registration_date' => now()->subDays(rand(1, 100)),
                'service_start_date' => now()->subDays(rand(1, 100)),
                'service_expiration_date' => now()->addDays(rand(1, 100)),
                'representative_contact_info' => '1234567890',
                'fax_number' => '1234567890',
                'organization_name' => 'Organization' . $i,
                'address' => 'Address' . $i,
                'name_of_representative' => 'Representative' . $i,
                'number_of_workers' => rand(1, 100),
                'homepage' => 'http://www.organization' . $i . '.com',
                'hr_management_usage' => 'Yes',
            ]);

            // Create related person in charge information
            PersonInChargeInformationGeneralPurpose::create([
                'user_general_purpose_id' => $user->id,
                'name' => 'Person' . $i,
                'job_title' => 'Job Title' . $i,
                'position' => 'Position' . $i,
                'department' => 'Department' . $i,
                'direct_number' => '1234567890',
                'cell_phone_number' => '0987654321',
                'email' => 'person' . $i . '@organization' . $i . '.com',
                'receive_email' => $i % 2 == 0,
                'receive_sms' => $i % 2 == 0,
            ]);

            // Create related additional information
            AdditionalInformationGeneralPurpose::create([
                'user_general_purpose_id' => $user->id,
                'business_number' => '1234567890',
                'corporate_number' => '0987654321',
                'type_of_business' => 'Business Type' . $i,
                'business_type' => 'Business' . $i,
                'email_receiving_tax_invoice' => 'tax' . $i . '@organization' . $i . '.com',
                'receive_email' => $i % 2 == 0,
                'data_document_output' => 'Use',
            ]);

            // Create related service subscription information
            ServiceSubscriptionInformationGeneralPurpose::create([
                'user_general_purpose_id' => $user->id,
                'subscription_service' => 'Basic',
                'start_date_of_use' => now()->subDays(rand(1, 100)),
                'expiration_date' => now()->addDays(rand(1, 100)),
                'number_of_service_users' => rand(1, 100),
                'usage_period_months' => 12,
                'service_fee' => rand(1000, 5000),
            ]);
        }
    }
}