<?php

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns paginated testimonials in the correct envelope', function () {
    Testimonial::factory()->count(20)->create();

    $response = $this->getJson('/api/v1/testimonials');

    $response->assertOk();
    expect($response->json())->toMatchPaginatedEnvelope();
    expect($response->json('data.records'))->toHaveCount(15);
    expect($response->json('data.total'))->toBe(20);
});

it('filters testimonials by is_featured', function () {
    Testimonial::factory()->count(5)->create(['is_featured' => true]);
    Testimonial::factory()->count(10)->create(['is_featured' => false]);

    $response = $this->getJson('/api/v1/testimonials?is_featured=true');

    $response->assertOk();
    expect($response->json())->toMatchPaginatedEnvelope();
    expect($response->json('data.total'))->toBe(5);
});
