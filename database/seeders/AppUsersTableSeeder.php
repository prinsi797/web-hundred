<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AppUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('app_users')->delete();
        
        \DB::table('app_users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Nombre',
                'dob' => '1996-04-20',
            'phone_number' => '3434343434',
                'security_code' => '00000',
                'profile_photo_url' => '20240420154131379.jpeg',
                'username' => 'nomresab',
                'deleted_at' => NULL,
                'created_at' => '2024-04-20 15:41:31',
                'updated_at' => '2024-04-20 15:41:31',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Akram Chauhan',
                'dob' => '1996-04-19',
            'phone_number' => '4454545444',
                'security_code' => '00000',
                'profile_photo_url' => '20240420154333857.jpeg',
                'username' => 'akramchauhan',
                'deleted_at' => NULL,
                'created_at' => '2024-04-20 15:43:33',
                'updated_at' => '2024-04-20 15:43:33',
            ),
        ));
        
        
    }
}