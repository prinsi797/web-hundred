<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolsTableSeeder extends Seeder {

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run() {


        DB::table('schools')->delete();

        DB::table('schools')->insert(
            [
                [
                    'name' => 'School 1',
                    'image_url' => '20240420154511116.jpeg',
                    'short_name' => 'SCHOOL1',
                    'website' => 'school1.com',
                    'street' => '123 main street',
                    'street2' => '122',
                    'zipcode' => '93222',
                    'state' => 'CA',
                    'city' => 'city',
                    'deleted_at' => NULL,
                    'created_at' => '2024-04-20 15:45:11',
                    'updated_at' => '2024-04-20 15:45:11',
                ],
                [
                    'name' => 'School 2',
                    'image_url' => '20240420154511116.jpeg',
                    'short_name' => 'SCHOOL2',
                    'website' => 'school2.com',
                    'street' => '123 main street',
                    'street2' => '122',
                    'zipcode' => '93222',
                    'state' => 'CA',
                    'city' => 'city',
                    'deleted_at' => NULL,
                    'created_at' => '2024-04-20 15:45:11',
                    'updated_at' => '2024-04-20 15:45:11',
                ],
                
            ]
        );
    }
}
