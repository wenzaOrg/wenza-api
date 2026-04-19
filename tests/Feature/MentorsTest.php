<?php

use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns paginated mentors in the correct envelope', function () {
    Mentor::factory()->count(20)->create();

    $response = $this->getJson('/api/v1/mentors');

    $response->assertOk();
    expect($response->json())->toMatchPaginatedEnvelope();
    expect($response->json('data.records'))->toHaveCount(15);
    expect($response->json('data.total'))->toBe(20);
});

it('filters mentors by course_id', function () {
    $mentor1 = Mentor::factory()->create();
    $mentor2 = Mentor::factory()->create();
    $mentor3 = Mentor::factory()->create();

    $course = Course::factory()->create();

    $mentor1->courses()->attach($course->id);
    $mentor2->courses()->attach($course->id);

    $response = $this->getJson("/api/v1/mentors?course_id={$course->id}");

    $response->assertOk();
    expect($response->json())->toMatchPaginatedEnvelope();
    expect($response->json('data.total'))->toBe(2);
});
