<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserCustomer;

class UpdateRoomIDDoctorNameSeederUserCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = UserCustomer::where('id','=',1)->first();
        $user->doctor_name = "Hong Gil-dong";
        $user->room_number = "101";
        $user->save();
    }
}