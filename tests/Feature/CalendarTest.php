<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Carbon;

test('the week downloads as a calendar', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-20 09:00', 'Africa/Porto-Novo'));

    $user = User::factory()->create(['timezone' => 'Africa/Porto-Novo']);
    Event::factory()->for($user)->create([
        'title' => 'Réseaux',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-20 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-20 16:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user)
        ->get(route('planning.calendar'))
        ->assertOk()
        ->assertHeader('content-type', 'text/calendar; charset=utf-8')
        ->assertSee('SUMMARY:Réseaux');

    Carbon::setTestNow();
});
