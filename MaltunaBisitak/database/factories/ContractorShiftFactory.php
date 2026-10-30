<?php

namespace Database\Factories;

use App\Models\Contractor;
use App\Models\ContractorShift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContractorShift>
 */
class ContractorShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contractor_id' => Contractor::factory(),
            'checked_in_at' => now(),
            'checked_out_at' => null,
            'needs_key' => false,
            'key_returned' => null,
            'signature_path' => null,
        ];
    }

    public function withKey(): static
    {
        return $this->state(fn (array $attributes) => [
            'needs_key' => true,
        ]);
    }

    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'checked_out_at' => now(),
            'key_returned' => $attributes['needs_key'] ? true : null,
        ]);
    }
}
