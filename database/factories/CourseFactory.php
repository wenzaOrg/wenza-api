<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'title' => ucwords($title),
            'category' => fake()->randomElement(['engineering', 'data', 'design', 'business', 'security']),
            'description' => fake()->paragraph(3),
            'duration_weeks' => fake()->numberBetween(8, 16),
            'format' => 'cohort',
            'price_ngn' => fake()->numberBetween(90000, 200000),
            'price_usd' => fake()->numberBetween(60, 150),
            'scholarship_price_ngn' => fake()->numberBetween(9000, 20000),
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }
}
