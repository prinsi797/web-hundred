<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'dob' => $this->faker->date($format = 'Y-m-d', $max = '2000-01-01'),
            'phone_number' => $this->faker->numerify('##########'), // Generates a random 10-digit number
            'security_code' => $this->faker->randomNumber(5),
            'profile_photo_url' => $this->faker->imageUrl(),
            'username' => $this->faker->userName,
            'lift_type' => $this->faker->randomElement(['power_clean']),
        ];
    }
}
