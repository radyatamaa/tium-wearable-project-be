<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserCustomerGatewayClient;

class CreateUserGateway extends Command
{
    /**
     * The name and signature of the console command.
     * example : php artisan create:user-gateway MC806599A50178 dlsgf5
     * @var string
     */
    protected $signature = 'create:user-gateway {sn?} {digit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create user gateway description';

    /**
     * Execute the console command.
     */

    public function generate_secret_key($serial_number, $predefined_string)
    {
        // Concatenate the serial number with the predefined string
        $concatenated_string = $serial_number . $predefined_string;

        // Perform SHA-256 hashing
        $secret_key = hash('sha256', $concatenated_string);

        return $secret_key;
    }
    public function handle()
    {
        $sn = $this->argument('sn');
        $digit = $this->argument('digit');

        if ($sn && $digit) {
            $secret = $this->generate_secret_key($sn, $digit);
            UserCustomerGatewayClient::create([
                'serial_number_device' => $sn,
                'secret_key' => $secret,
                'digit' => $digit,
            ]);
            $this->info("Generating user gateway success : sn : {$sn} , digit : {$digit},secret : {$secret}");
            return;
        }

        $this->info("Generating user gateway failed");
    }
}