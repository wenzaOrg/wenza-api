<?php

namespace Database\Seeders;

use App\Models\Cohort;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CohortSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing cohorts
        Schema::disableForeignKeyConstraints();
        Cohort::truncate();
        Schema::enableForeignKeyConstraints();

        $courses = Course::all();

        foreach ($courses as $course) {
            // Phoenix 2026 Intake
            $phoenixStart = Carbon::parse('2026-05-25');
            $phoenixEnd = $phoenixStart->copy()->addWeeks($course->duration_weeks);

            Cohort::create([
                'course_id' => $course->id,
                'name' => 'Phoenix 2026',
                'start_date' => $phoenixStart->toDateString(),
                'end_date' => $phoenixEnd->toDateString(),
                'capacity' => 50,
                'status' => 'upcoming',
            ]);

            // Comet 2026 Intake
            $cometStart = Carbon::parse('2026-09-30');
            $cometEnd = $cometStart->copy()->addWeeks($course->duration_weeks);

            Cohort::create([
                'course_id' => $course->id,
                'name' => 'Comet 2026',
                'start_date' => $cometStart->toDateString(),
                'end_date' => $cometEnd->toDateString(),
                'capacity' => 50,
                'status' => 'upcoming',
            ]);
        }
    }
}
