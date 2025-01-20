<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Announcement\Active;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->realText(20),
            'content' => $this->faker->realText(1000),
            'start_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'active' => $this->faker->randomElement(Active::all()),
        ];
    }
}
