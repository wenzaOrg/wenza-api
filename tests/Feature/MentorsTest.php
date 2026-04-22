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

it('filters mentors by course category', function () {
    // We need courses with specific categories
    $engineeringCourse = Course::factory()->create(['category' => 'engineering']);
    $dataCourse = Course::factory()->create(['category' => 'data']);

    $engMentor = Mentor::factory()->create();
    $dataMentor = Mentor::factory()->create();

    $engMentor->courses()->attach($engineeringCourse->id);
    $dataMentor->courses()->attach($dataCourse->id);

    $response = $this->getJson('/api/v1/mentors?category=engineering');
    $response->assertOk();

    $returnedIds = collect($response->json('data.records'))->pluck('id');
    expect($returnedIds)->toContain($engMentor->id);
    expect($returnedIds)->not->toContain($dataMentor->id);
});
