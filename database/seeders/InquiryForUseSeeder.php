<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InquiryForUse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class InquiryForUseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Truncate the table before seeding
       DB::table('inquiry_for_uses')->truncate();

       $faker = \Faker\Factory::create();

       foreach (range(1, 100) as $index) {
        InquiryForUse::insert([
               'username' => $faker->userName,
               'organization' => $faker->company,
               'user_type' => $faker->randomElement(['의료기관', '산업연장', '공공복지']),
               'inquirer' => $faker->name,
               'title' => $faker->sentence,
               'status' => $faker->randomElement(['접수중', '처리완료', '미확인']),
               'registration_date' => $faker->date(),
               'response_date' => $faker->optional()->date(),
               'responder' => $faker->optional()->name,
               'inquiry_details' => $faker->optional()->paragraph,
               'response_details' => $faker->optional()->paragraph,
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now(),
           ]);
       }
    }
}