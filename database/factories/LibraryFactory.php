<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Library>
 */
class LibraryFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name,
            'address_line_1' => fake()->streetAddress,
            'address_line_2' => null,
            'city' => fake()->city,
            'county' => ucfirst(fake()->word),
            'postcode' => fake()->postcode,
        ];
    }
}
