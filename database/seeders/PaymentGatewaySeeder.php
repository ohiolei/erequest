<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_gateways = array(
           
            array('id' => '2','gateway_name' => 'Ogun BPMS','gateway_slug' => 'ticrms','merchant_id' => '10002','public_key' => 'PUB_KEY_WMZEYBRU1O1RSSPYG04ARYCDI4FTCUKJ','private_key' => 'SEC_KEY_HFLGOUDVBEJW9PRQ1WDUVJ6YS2U4BKP9','hash_type' => 'sha256','currency' => 'NGN','status' => 'active','created_at' => '2023-01-19 10:44:17','updated_at' => '2023-01-19 10:44:17')
          );

          DB::table('payment_gateways')->insert($payment_gateways);


    }
}
