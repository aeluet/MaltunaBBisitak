<?php

namespace Database\Factories;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'origin' => fake()->company(),
            'country' => fake()->randomElement(['ES', 'FR', 'PT', 'DE']),
            'document_number' => null,
            'phone' => null,
            'notes' => null,
        ];
    }
}
