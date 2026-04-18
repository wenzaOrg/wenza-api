<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

describe('POST /api/v1/auth/register', function () {
    it('registers a new student and returns the auth envelope', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Chidi',
            'last_name' => 'Okafor',
            'email' => 'chidi@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertStatus(201);
        expect($response->json())->toMatchSuccessEnvelope();
        expect($response->json('data'))->toHaveKeys(['user', 'token']);
        expect($response->json('data.user.email'))->toBe('chidi@example.com');
        expect($response->json('message'))->toBe('Registration successful');
    });

    it('returns 422 with error envelope for duplicate email', function () {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'taken@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertStatus(422);
        expect($response->json())->toMatchErrorEnvelope();
        expect($response->json('errors'))->toHaveKey('email');
    });

    it('returns 422 when password does not meet policy', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'weak@example.com',
            'password' => 'weakpassword',
            'password_confirmation' => 'weakpassword',
        ]);

        $response->assertStatus(422);
        expect($response->json())->toMatchErrorEnvelope();
    });
});

describe('POST /api/v1/auth/login', function () {
    it('returns user and token on valid credentials', function () {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('Password1!'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'student@example.com',
            'password' => 'Password1!',
        ]);

        $response->assertOk();
        expect($response->json())->toMatchSuccessEnvelope();
        expect($response->json('data'))->toHaveKeys(['user', 'token']);
        expect($response->json('message'))->toBe('Login successful');
    });

    it('returns 401 on invalid credentials', function () {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nobody@example.com',
            'password' => 'WrongPassword1!',
        ]);

        $response->assertStatus(401);
        expect($response->json())->toMatchErrorEnvelope();
    });
});

describe('GET /api/v1/auth/me', function () {
    it('returns the authenticated user', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me');

        $response->assertOk();
        expect($response->json())->toMatchSuccessEnvelope();
        expect($response->json('data.email'))->toBe($user->email);
    });

    it('returns 401 with the exact Unauthenticated message when not authenticated', function () {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
        expect($response->json())->toMatchErrorEnvelope();
        expect($response->json('message'))->toContain('Unauthenticated');
    });
});

describe('POST /api/v1/auth/logout', function () {
    it('revokes the token and returns success envelope', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk();
        expect($response->json())->toMatchSuccessEnvelope();
        expect($response->json('message'))->toBe('Logged out');
    });
});
