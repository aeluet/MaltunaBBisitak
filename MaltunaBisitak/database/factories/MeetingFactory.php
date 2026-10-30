<?php

namespace Database\Factories;

use App\Models\Meeting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meeting>
 */
class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'location' => fake()->randomElement(['Areto nagusia', 'Bilera gela', 'Liburutegia']),
            'date' => fake()->dateTimeBetween('now', '+1 month'),
            'starts_at' => fake()->time('H:i'),
        ];
    }
}
