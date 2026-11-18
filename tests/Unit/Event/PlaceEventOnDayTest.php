<?php

declare(strict_types=1);

use App\Actions\Event\PlaceEventOnDay;
use Carbon\Carbon;

test('a block is placed from the start of the visible day', function () {
    $placed = (new PlaceEventOnDay)(
        Carbon::parse('2026-11-19 14:00', 'UTC'),
        Carbon::parse('2026-11-19 15:30', 'UTC'),
        Carbon::parse('2026-11-19 06:00', 'UTC'),
        Carbon::parse('2026-11-19 23:00', 'UTC'),
    );

    expect($placed)->toBe([
        'top' => 48.0,
        'height' => 9.0,
    ]);
});

test('a block outside the visible hours is left out', function () {
    $placed = (new PlaceEventOnDay)(
        Carbon::parse('2026-11-19 23:00', 'UTC'),
        Carbon::parse('2026-11-19 23:30', 'UTC'),
        Carbon::parse('2026-11-19 06:00', 'UTC'),
        Carbon::parse('2026-11-19 23:00', 'UTC'),
    );

    expect($placed)->toBeNull();
});
