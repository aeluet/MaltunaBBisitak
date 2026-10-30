<?php

namespace Database\Factories;

use App\Enums\VisitStatus;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visit>
 */
class VisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'visit_date' => fake()->dateTimeBetween('now', '+1 month'),
            'origin' => fake()->company(),
            'is_group' => false,
            'group_size' => null,
            'status' => VisitStatus::Pending,
            'locale' => 'eu',
        ];
    }

    public function group(int $size = 20): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
            'group_size' => $size,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VisitStatus::Approved,
            'reviewed_by' => User::factory()->admin(),
            'reviewed_at' => now(),
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'visit_date' => today(),
        ]);
    }
}
