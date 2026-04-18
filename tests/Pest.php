<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Pest Configuration + Reusable Custom Matchers
|--------------------------------------------------------------------------
|
| These matchers are used by all feature tests to assert envelope compliance
| per wenza-api/README.md §12.3.
|
*/

uses(TestCase::class)->in('Feature');

/*
| Asserts the response conforms to the success envelope:
| { status: "success", message: string, data: any }
*/
expect()->extend('toMatchSuccessEnvelope', function () {
    return $this
        ->toHaveKey('status', 'success')
        ->toHaveKey('message')
        ->toHaveKey('data');
});

/*
| Asserts the response conforms to the paginated envelope.
| data.records MUST be present — not data.items or data.data.
*/
expect()->extend('toMatchPaginatedEnvelope', function () {
    $this->toMatchSuccessEnvelope();

    expect($this->value['data'])->toHaveKeys([
        'records',
        'current_page',
        'last_page',
        'per_page',
        'total',
        'next_page_url',
        'prev_page_url',
        'links',
    ]);

    return $this;
});

/*
| Asserts the response conforms to the error envelope:
| { status: "error", message: string }
*/
expect()->extend('toMatchErrorEnvelope', function () {
    return $this
        ->toHaveKey('status', 'error')
        ->toHaveKey('message');
});
