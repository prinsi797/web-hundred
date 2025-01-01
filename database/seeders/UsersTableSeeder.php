<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    { 
        \DB::table('users')->delete();

        \DB::table('users')->insert(array(
            0 =>
            array(
                'id' => 1,
                'first_name' => 'Anthony',
                'last_name' => 'Privitelli',
                'email' => 'anthony.privitelli@gmail.com',
                'company' => NULL,
                'street' => NULL,
                'street2' => NULL,
                'city' => NULL,
                'state' => NULL,
                'zipcode' => NULL,
                'password' => bcrypt('Tech@2025'),
                'phone_number' => NULL,
                'is_report_shared' => 0,
                'is_non_profit' => 0,
                'country_code' => NULL,
                'email_verified_at' => NULL,
                'two_factor_enable' => 0,
                'two_factor_code' => NULL,
                'two_factor_expires_at' => NULL,
                'remember_token' => 'dg9gvN2HzCUDt0JhN2XpkEQqofPcMxYfwMALvGGMxGODhj800IHWaBoSmnz6',
                'deleted_at' => NULL,
                'created_at' => '2023-10-14 00:10:58',
                'updated_at' => '2024-04-01 17:14:47',
            ),
            1 =>
            array(
                'id' => 4,
                'first_name' => 'Akram',
                'last_name' => 'Chauhan',
                'email' => 'akram@kryzetech.com',
                'company' => NULL,
                'street' => NULL,
                'street2' => NULL,
                'city' => NULL,
                'state' => NULL,
                'zipcode' => NULL,
                'password' => bcrypt('Tech@2025'),
                'phone_number' => NULL,
                'is_report_shared' => 0,
                'is_non_profit' => 0,
                'country_code' => NULL,
                'email_verified_at' => NULL,
                'two_factor_enable' => 0,
                'two_factor_code' => NULL,
                'two_factor_expires_at' => NULL,
                'remember_token' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2023-10-15 09:05:44',
                'updated_at' => '2024-04-01 17:05:59',
            ),
        ));
    }
}