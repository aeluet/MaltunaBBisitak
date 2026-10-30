<?php

namespace Database\Factories;

use App\Models\AvailableDate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvailableDate>
 */
class AvailableDateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->unique()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'max_visits' => null,
            'notes' => null,
        ];
    }
}
