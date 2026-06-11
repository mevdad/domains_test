<?php

namespace Database\Factories;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Domain>
 */
class DomainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->unique()->domainName(),
            'check_interval' => fake()->randomElement([1, 5, 10, 15, 30, 60]),
            'check_timeout' => fake()->numberBetween(1, 60),
            'check_method' => fake()->randomElement(['GET', 'HEAD']),
        ];
    }
}
