<?php

namespace Database\Factories;

use App\Models\VisitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitType>
 */
class VisitTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name_eu' => $name,
            'name_es' => $name,
            'name_en' => $name,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
