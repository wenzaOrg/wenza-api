<?php

use App\Jobs\SendLeadNotification;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('stores a lead and returns success envelope with reference', function () {
    Queue::fake();

    $course = Course::factory()->create();

    $payload = [
        'full_name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '+2348000000000',
        'course_id' => $course->id,
        'referral_source' => 'twitter',
        'motivation' => 'I want to learn software development.',
    ];

    $response = $this->postJson('/api/v1/leads', $payload);

    $response->assertStatus(201);
    expect($response->json())->toMatchSuccessEnvelope();
    expect($response->json('data.full_name'))->toBe('Jane Doe');
    expect($response->json('data.reference'))->toStartWith('WZL-');
    expect($response->json('data.status'))->toBe('new');

    $this->assertDatabaseHas('leads', [
        'email' => 'jane@example.com',
        'status' => 'new',
    ]);

    Queue::assertPushed(SendLeadNotification::class);
});

it('validates required fields and returns 422 error envelope', function () {
    $response = $this->postJson('/api/v1/leads', []);

    $response->assertStatus(422);
    expect($response->json())->toMatchErrorEnvelope();
    expect($response->json('message'))->toContain('full name field is required');
});
