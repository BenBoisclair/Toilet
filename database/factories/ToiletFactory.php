<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Toilet>
 */
class ToiletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(5),
            'location' => fake()->streetAddress(),
            'ratings' => fake()->numberBetween(1, 5),
            'latitude' => fake()->latitude(13.5, 13.9),
            'longitude' => fake()->longitude(100.3, 100.9),
            'description' => fake()->paragraph(),
            'discoverer_id' => User::factory(),
        ];
    }
}
