<?php

use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns paginated courses in the correct envelope', function () {
    Course::factory()->count(20)->create(['is_published' => true]);

    $response = $this->getJson('/api/v1/courses');

    $response->assertOk();
    expect($response->json())->toMatchPaginatedEnvelope();
    // Default per_page is 15, so first page should have 15 records
    expect($response->json('data.records'))->toHaveCount(15);
    expect($response->json('data.total'))->toBe(20);
});

it('filters courses by category', function () {
    Course::factory()->count(5)->create(['is_published' => true, 'category' => 'engineering']);
    Course::factory()->count(3)->create(['is_published' => true, 'category' => 'data']);

    $response = $this->getJson('/api/v1/courses?category=engineering');

    $response->assertOk();
    expect($response->json())->toMatchPaginatedEnvelope();
    expect($response->json('data.total'))->toBe(5);
});

it('excludes unpublished courses from the catalog', function () {
    Course::factory()->count(5)->create(['is_published' => true]);
    Course::factory()->count(3)->create(['is_published' => false]);

    $response = $this->getJson('/api/v1/courses');

    $response->assertOk();
    expect($response->json('data.total'))->toBe(5);
});

it('returns featured courses', function () {
    Course::factory()->count(3)->create(['is_published' => true, 'is_featured' => true]);
    Course::factory()->count(5)->create(['is_published' => true, 'is_featured' => false]);

    $response = $this->getJson('/api/v1/courses/featured');

    $response->assertOk();
    expect($response->json())->toMatchSuccessEnvelope();
    expect($response->json('data'))->toHaveCount(3);
});

it('returns a single course with modules, cohorts, and mentors', function () {
    $course = Course::factory()->create(['is_published' => true, 'slug' => 'software-development']);
    $mentor = Mentor::factory()->create();
    $course->mentors()->attach($mentor->id);

    $response = $this->getJson('/api/v1/courses/software-development');

    $response->assertOk();
    expect($response->json())->toMatchSuccessEnvelope();
    expect($response->json('data.slug'))->toBe('software-development');
    expect($response->json('data'))->toHaveKeys(['modules', 'cohorts', 'mentors']);
    expect($response->json('data.mentors'))->toHaveCount(1);
});

it('returns 404 for unknown course slug', function () {
    $response = $this->getJson('/api/v1/courses/does-not-exist');

    $response->assertStatus(404);
    expect($response->json())->toMatchErrorEnvelope();
});

it('returns 401 with the exact Unauthenticated message on protected route', function () {
    $response = $this->getJson('/api/v1/auth/me');

    $response->assertStatus(401);
    expect($response->json())->toMatchErrorEnvelope();
    expect($response->json('message'))->toContain('Unauthenticated');
});
