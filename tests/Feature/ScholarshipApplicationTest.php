<?php

use App\Jobs\NotifyAdminOfNewScholarshipApplication;
use App\Jobs\SendScholarshipApplicantConfirmation;
use App\Models\Cohort;
use App\Models\Course;
use App\Models\ScholarshipApplication;
use App\Services\TurnstileVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\RateLimiter;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    Bus::fake();

    // Clear rate limiters for the testing IP
    RateLimiter::clear('scholarship-burst:127.0.0.1');
    RateLimiter::clear('scholarship-burst:');

    // Create a course and cohort for testing
    $this->course = Course::factory()->create(['title' => 'Software Development']);
    $this->cohort = Cohort::factory()->create([
        'course_id' => $this->course->id,
        'name' => 'Phoenix 2026',
        'status' => 'upcoming',
    ]);

    // Mock successful Turnstile by default
    $this->mock(TurnstileVerifier::class, function (MockInterface $mock) {
        $mock->shouldReceive('verify')->andReturn(true);
    });
});

it('submits a valid scholarship application and creates a record', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@test.com',
        'phone' => '+2348012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/scholarship-applications', $data);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Scholarship application submitted successfully')
        ->assertJsonStructure(['data' => ['reference_code']]);

    $this->assertDatabaseHas('scholarship_applications', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@test.com',
        'pipeline_status' => 'new',
    ]);
});

it('dispatches confirmation and admin notification jobs', function () {
    $data = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@test.com',
        'phone' => '07011122233',
        'gender' => 'female',
        'country' => 'United Kingdom',
        'state_or_city' => 'London',
        'current_status' => 'employed',
        'education_level' => 'masters',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'hybrid',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'some',
        'wants_job_placement' => false,
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/scholarship-applications', $data);

    Bus::assertDispatched(SendScholarshipApplicantConfirmation::class);
    Bus::assertDispatched(NotifyAdminOfNewScholarshipApplication::class);
});

it('generates a unique reference_code starting with SCH-', function () {
    $data = [
        'first_name' => 'Ref',
        'last_name' => 'Tester',
        'email' => 'ref@test.com',
        'phone' => '08012345678',
        'gender' => 'prefer_not_to_say',
        'country' => 'Ghana',
        'state_or_city' => 'Accra',
        'current_status' => 'student',
        'education_level' => 'high_school',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'physical',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/scholarship-applications', $data);
    $application = ScholarshipApplication::where('email', 'ref@test.com')->first();

    expect($application->reference_code)->toMatch('/^SCH-[A-Z0-9]{6}$/');
});

it('persists pipeline_status as new on creation', function () {
    $data = [
        'first_name' => 'Status',
        'last_name' => 'Tester',
        'email' => 'status@test.com',
        'phone' => '08012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Abuja',
        'current_status' => 'unemployed',
        'education_level' => 'hnd',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/scholarship-applications', $data);
    $application = ScholarshipApplication::where('email', 'status@test.com')->first();

    expect($application->pipeline_status)->toBe('new');
});

it('returns 422 when email is invalid', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'not-an-email',
        'phone' => '08012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];
    $this->postJson('/api/v1/scholarship-applications', $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when phone is malformed', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@test.com',
        'phone' => 'abc123',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];
    $this->postJson('/api/v1/scholarship-applications', $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['phone']);
});

it('returns 422 when course_id does not exist', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@test.com',
        'phone' => '08012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => 9999,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];
    $this->postJson('/api/v1/scholarship-applications', $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['course_id']);
});

it('returns 422 when cohort_id does not exist', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@test.com',
        'phone' => '08012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => $this->course->id,
        'cohort_id' => 9999,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];
    $this->postJson('/api/v1/scholarship-applications', $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['cohort_id']);
});

it('returns 422 when turnstile token is missing', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@test.com',
        'phone' => '08012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
    ];
    $this->postJson('/api/v1/scholarship-applications', $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['turnstile_token']);
});

it('returns 422 when turnstile token is invalid', function () {
    $this->mock(TurnstileVerifier::class, function (MockInterface $mock) {
        $mock->shouldReceive('verify')->andReturn(false);
    });

    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@test.com',
        'phone' => '+2348012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'invalid-token',
    ];

    $this->postJson('/api/v1/scholarship-applications', $data)
        ->assertStatus(422)
        ->assertJsonPath('message', 'Security verification failed. Please try again.');
});

it('rate limits after 2 attempts per minute from same IP', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@test.com',
        'phone' => '+2348012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];

    // First 2 should pass
    for ($i = 0; $i < 2; $i++) {
        $this->postJson('/api/v1/scholarship-applications', $data)->assertStatus(201);
    }

    // 3rd should be throttled
    $this->postJson('/api/v1/scholarship-applications', $data)->assertStatus(429);
});

it('correctly links to the course and cohort via relations', function () {
    $data = [
        'first_name' => 'Relation',
        'last_name' => 'Tester',
        'email' => 'rel@test.com',
        'phone' => '+2348012345678',
        'gender' => 'male',
        'country' => 'Nigeria',
        'state_or_city' => 'Lagos',
        'current_status' => 'graduate',
        'education_level' => 'degree',
        'course_id' => $this->course->id,
        'cohort_id' => $this->cohort->id,
        'learning_mode' => 'online',
        'wants_scholarship' => true,
        'prior_tech_experience' => 'none',
        'wants_job_placement' => true,
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/scholarship-applications', $data);
    $application = ScholarshipApplication::where('email', 'rel@test.com')->first();

    expect($application->course->id)->toBe($this->course->id);
    expect($application->cohort->id)->toBe($this->cohort->id);
});
