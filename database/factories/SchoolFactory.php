<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'image_url' => $this->faker->imageUrl(),
            'short_name' => $this->faker->word,
            'website' => $this->faker->url,
            'street' => $this->faker->streetAddress,
            'street2' => $this->faker->secondaryAddress,
            'zipcode' => $this->faker->postcode,
            'state' => $this->faker->state,
            'city' => $this->faker->city,
        ];
    }
}
