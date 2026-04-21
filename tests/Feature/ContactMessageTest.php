<?php

use App\Jobs\NotifyAdminOfNewContactMessage;
use App\Services\TurnstileVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\RateLimiter;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    Bus::fake();

    // Clear rate limiters for the testing IP
    RateLimiter::clear('contact-messages:127.0.0.1');
    RateLimiter::clear('contact-messages:'); // Sometimes IP is null in tests depending on middleware

    // Mock successful Turnstile by default
    $this->mock(TurnstileVerifier::class, function (MockInterface $mock) {
        $mock->shouldReceive('verify')->andReturn(true);
    });
});

it('submits a valid contact message and persists it', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'subject' => 'application_question',
        'message' => 'I have a question about applying to the programme. Can you provide more details?',
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/contact-messages', $data);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Message sent successfully')
        ->assertJsonStructure(['data' => ['reference_code']]);

    $this->assertDatabaseHas('contact_messages', [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'subject' => 'application_question',
        'is_read' => false,
    ]);
});

it('dispatches admin notification job on success', function () {
    $data = [
        'full_name' => 'Jane Smith',
        'email' => 'jane@test.com',
        'subject' => 'scholarship_question',
        'message' => 'How do I apply for a scholarship? What are the requirements?',
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/contact-messages', $data);

    Bus::assertDispatched(NotifyAdminOfNewContactMessage::class);
});

it('generates a unique reference_code starting with MSG-', function () {
    $data = [
        'full_name' => 'Test User',
        'email' => 'test@test.com',
        'subject' => 'other',
        'message' => 'This is a test message to check reference code generation.',
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/contact-messages', $data);

    $response->assertStatus(201);

    $referenceCode = $response->json('data.reference_code');
    expect($referenceCode)->toStartWith('MSG-');
    expect(strlen($referenceCode))->toBe(10); // MSG- + 6 chars

    $this->assertDatabaseHas('contact_messages', [
        'reference_code' => $referenceCode,
    ]);
});

it('defaults is_read to false on creation', function () {
    $data = [
        'full_name' => 'Unread Test',
        'email' => 'unread@test.com',
        'subject' => 'press_partnerships',
        'message' => 'I am interested in partnering with Wenza for press coverage.',
        'turnstile_token' => 'valid-token',
    ];

    $this->postJson('/api/v1/contact-messages', $data);

    $this->assertDatabaseHas('contact_messages', [
        'is_read' => false,
    ]);
});

it('returns 422 when email is invalid', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'not-an-email',
        'subject' => 'application_question',
        'message' => 'Valid message text for validation test.',
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/contact-messages', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when message is under 20 chars', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'subject' => 'application_question',
        'message' => 'Short message',
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/contact-messages', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['message']);
});

it('returns 422 when message is over 2000 chars', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'subject' => 'application_question',
        'message' => str_repeat('A', 2001),
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/contact-messages', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['message']);
});

it('returns 422 when subject is not in allowed enum', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'subject' => 'invalid_subject',
        'message' => 'Valid message text for validation test.',
        'turnstile_token' => 'valid-token',
    ];

    $response = $this->postJson('/api/v1/contact-messages', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['subject']);
});

it('returns 422 when turnstile token is missing', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'subject' => 'application_question',
        'message' => 'Valid message text for validation test.',
    ];

    $response = $this->postJson('/api/v1/contact-messages', $data);

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
        'subject' => 'application_question',
        'message' => 'Valid message text for validation test.',
        'turnstile_token' => 'invalid-token',
    ];

    $response = $this->postJson('/api/v1/contact-messages', $data);

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Security verification failed. Please try again.');
});

it('rate limits after 3 attempts per minute from same IP', function () {
    $data = [
        'full_name' => 'John Doe',
        'email' => 'john@test.com',
        'subject' => 'application_question',
        'message' => 'Valid message text for rate limiting test.',
        'turnstile_token' => 'valid-token',
    ];

    // First 3 should pass
    for ($i = 0; $i < 3; $i++) {
        $this->postJson('/api/v1/contact-messages', $data)->assertStatus(201);
    }

    // 4th should be throttled
    $this->postJson('/api/v1/contact-messages', $data)->assertStatus(429);
});
