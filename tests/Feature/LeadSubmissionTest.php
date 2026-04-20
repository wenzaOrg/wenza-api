<?php

use App\Jobs\NotifyAdminOfNewLead;
use App\Jobs\SendApplicantConfirmation;
use App\Models\Lead;
use App\Services\TurnstileVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\RateLimiter;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    Bus::fake();

    // Clear rate limiters for the testing IP
    RateLimiter::clear('leads:127.0.0.1');
    RateLimiter::clear('leads:'); // Sometimes IP is null in tests depending on middleware

    // Mock successful Turnstile by default
    $this->mock(TurnstileVerifier::class, function (MockInterface $mock) {
        $mock->shouldReceive('verify')->andReturn(true);
    });
});

it('submits a valid application and creates a lead', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'I want to learn how to build production-ready web applications.',
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/leads', $data);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Application submitted successfully')
        ->assertJsonStructure(['data' => ['reference_code']]);

    $this->assertDatabaseHas('leads', [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'pipeline_status' => 'new',
    ]);
});

it('dispatches confirmation and admin notification jobs on success', function () {
    $data = [
        'full_name' => 'Jane Smith',
        'email' => 'jane@test.com',
        'phone' => '07011122233',
        'age' => 30,
        'employment_status' => 'self_employed',
        'education_level' => 'masters',
        'goals' => 'Looking to expand my technical skill set for my startup.',
        'wants_scholarship' => false,
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/leads', $data);

    Bus::assertDispatched(SendApplicantConfirmation::class);
    Bus::assertDispatched(NotifyAdminOfNewLead::class);
});

it('returns 422 when email is invalid', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'not-an-email',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'Valid goals text for validation test.',
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/leads', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when phone is not Nigerian format', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'phone' => '1234567890', // Invalid Nigerian format
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'Valid goals text for validation test.',
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/leads', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['phone']);
});

it('returns 422 when goals field is too short', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'Short', // Under 20 chars
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/leads', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['goals']);
});

it('returns 422 when turnstile token is missing', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'Valid goals text for validation test.',
        'wants_scholarship' => true,
    ];

    $response = $this->postJson('/api/v1/leads', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['turnstile_token']);
});

it('returns 422 when turnstile token is invalid', function () {
    $this->mock(TurnstileVerifier::class, function (MockInterface $mock) {
        $mock->shouldReceive('verify')->andReturn(false);
    });

    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'Valid goals text for validation test.',
        'wants_scholarship' => true,
        'turnstile_token' => 'invalid-token',
    ];

    $response = $this->postJson('/api/v1/leads', $data);

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Security verification failed. Please try again.');
});

it('rate limits after 3 attempts per minute from same IP', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'Valid goals text for validation test.',
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    // First 3 should pass
    for ($i = 0; $i < 3; $i++) {
        $this->postJson('/api/v1/leads', $data)->assertStatus(201);
    }

    // 4th should be throttled
    $this->postJson('/api/v1/leads', $data)->assertStatus(429);
});

it('accepts submission without course_id', function () {
    $data = [
        'full_name' => 'General Enquirer',
        'email' => 'general@test.com',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'unemployed',
        'education_level' => 'ssce',
        'goals' => 'I just want to inquire about available tracks.',
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/leads', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('leads', [
        'email' => 'general@test.com',
        'course_id' => null,
    ]);
});

it('requires guardian_consent when age is under 18', function () {
    $data = [
        'full_name' => 'Young Student',
        'email' => 'young@test.com',
        'phone' => '08012345678',
        'age' => 17,
        'employment_status' => 'student',
        'education_level' => 'ssce',
        'goals' => 'I want to start my tech journey early.',
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    // Should fail without consent
    $this->postJson('/api/v1/leads', $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['guardian_consent']);

    // Should pass with consent
    $data['guardian_consent'] = true;
    $this->postJson('/api/v1/leads', $data)->assertStatus(201);
});

it('generates a unique reference code on creation', function () {
    $data = [
        'full_name' => 'Ref Tester',
        'email' => 'ref@test.com',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'Testing reference code generation.',
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/leads', $data);
    $lead = Lead::where('email', 'ref@test.com')->first();

    expect($lead->reference_code)->toMatch('/^LEAD-[A-Z0-9]{6}$/');
});

it('persists pipeline_status as new on creation', function () {
    $data = [
        'full_name' => 'Status Tester',
        'email' => 'status@test.com',
        'phone' => '08012345678',
        'age' => 25,
        'employment_status' => 'student',
        'education_level' => 'ssce',
        'goals' => 'Testing initial pipeline status.',
        'wants_scholarship' => true,
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/leads', $data);
    $lead = Lead::where('email', 'status@test.com')->first();

    expect($lead->pipeline_status)->toBe('new');
});
