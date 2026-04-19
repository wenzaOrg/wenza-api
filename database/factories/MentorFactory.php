<?php

namespace Database\Factories;

use App\Models\Mentor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mentor>
 */
class MentorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'title' => fake()->jobTitle(),
            'bio' => fake()->paragraph(),
            'avatar_url' => fake()->imageUrl(),
            'linkedin_url' => fake()->url(),
            'years_experience' => fake()->numberBetween(3, 15),
        ];
    }
}
