<?php

namespace Database\Factories;

use App\Models\Cohort;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cohort>
 */
class CohortFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'name' => fake()->randomElement(['Phoenix 2026', 'Comet 2026']),
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(120),
            'capacity' => 50,
            'status' => 'upcoming',
        ];
    }
}
