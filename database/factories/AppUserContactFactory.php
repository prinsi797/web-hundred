<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppUserContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'app_user_id' => '1',
            'contact_firstname' => 'prinsi',
            'contact_lastname' => 'tilva',
            'contact_phone_number' => '7041134556',
        ];
    }
}
