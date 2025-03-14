<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->unique()->name,
            'description' => fake()->sentences(rand(1, 4), asText: true),
        ];
    }
}
