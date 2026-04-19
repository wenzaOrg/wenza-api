<?php

namespace Database\Factories;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source' => fake()->randomElement(['twitter', 'linkedin', 'manual']),
            'content' => fake()->paragraph(),
            'author_name' => fake()->name(),
            'author_role' => fake()->jobTitle(),
            'is_featured' => fake()->boolean(),
        ];
    }
}
