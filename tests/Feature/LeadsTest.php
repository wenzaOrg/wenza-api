<?php

use App\Jobs\SendApplicantConfirmation;
use App\Models\Course;
use App\Services\TurnstileVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores a lead and returns success envelope with reference', function () {
    Bus::fake();
    $this->mock(TurnstileVerifier::class, fn ($mock) => $mock->shouldReceive('verify')->andReturn(true));

    $course = Course::factory()->create();

    $payload = [
        'full_name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '08000000000',
        'age' => 25,
        'employment_status' => 'employed',
        'education_level' => 'bachelors',
        'goals' => 'I want to learn software development and build production ready apps.',
        'wants_scholarship' => true,
        'course_id' => $course->id,
        'turnstile_token' => 'mock-token',
    ];

    $response = $this->postJson('/api/v1/leads', $payload);

    $response->assertStatus(201);
    expect($response->json())->toMatchSuccessEnvelope();
    expect($response->json('data.reference_code'))->toMatch('/^LEAD-[A-Z0-9]{6}$/');

    $this->assertDatabaseHas('leads', [
        'email' => 'jane@example.com',
        'pipeline_status' => 'new',
    ]);

    Bus::assertDispatched(SendApplicantConfirmation::class);
});

it('validates required fields and returns 422 error envelope', function () {
    $response = $this->postJson('/api/v1/leads', []);

    $response->assertStatus(422);
    expect($response->json())->toMatchErrorEnvelope();
    expect($response->json('message'))->toContain('Please provide your full name.');
});
