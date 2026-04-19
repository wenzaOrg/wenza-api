<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CourseSeeder::class,
            MentorSeeder::class,
            TestimonialSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
