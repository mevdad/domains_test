<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\DomainCheck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DomainCheck>
 */
class DomainCheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isUp = fake()->boolean(80);

        return [
            'domain_id' => Domain::factory(),
            'is_up' => $isUp,
            'status_code' => $isUp ? 200 : null,
            'response_time_ms' => $isUp ? fake()->numberBetween(50, 2000) : null,
            'error' => $isUp ? null : fake()->sentence(),
            'checked_at' => now(),
        ];
    }
}
