<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppUserPowercleanFactory extends Factory
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
            'date' => '2021-10-10',
            'power_clean' => 550,
        ];
    }
}
