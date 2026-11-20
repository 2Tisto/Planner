<?php

use App\Livewire\Planning\PlanningBoard;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('the phone shows what is happening and what comes next', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-19 14:30', 'Africa/Porto-Novo'));

    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    Event::factory()->for($user)->create([
        'title' => 'Réseaux',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
    ]);
    Event::factory()->for($user)->create([
        'title' => 'Sport',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 17:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->assertSee('En ce moment')
        ->assertSee('Réseaux')
        ->assertSee('Ensuite')
        ->assertSee('Sport')
        ->call('openEdit', Event::query()->where('title', 'Réseaux')->firstOrFail()->id, '2026-11-19')
        ->assertSet('composerOpen', true)
        ->assertSet('title', 'Réseaux');

    Carbon::setTestNow();
});

test('a quiet day says the day is open', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-19 14:30', 'Africa/Porto-Novo'));

    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->assertSee('La journée est libre.')
        ->assertDontSee('Ensuite');

    Carbon::setTestNow();
});

test('before the next block the day says nothing is happening', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-19 13:00', 'Africa/Porto-Novo'));

    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    Event::factory()->for($user)->create([
        'title' => 'Réseaux',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->assertSee('Rien en ce moment.')
        ->assertSee('Ensuite')
        ->assertSee('Réseaux');

    Carbon::setTestNow();
});

test('a finished morning does not pretend the day was empty', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-20 14:00', 'Africa/Porto-Novo'));

    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    Event::factory()->for($user)->create([
        'title' => 'Réseaux',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-20 07:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-20 08:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->assertSee('Plus rien pour aujourd\'hui.')
        ->assertDontSee('La journée est libre.');

    Carbon::setTestNow();
});

test('another student does not appear in what is happening now', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-19 14:30', 'Africa/Porto-Novo'));

    $owner = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    Event::factory()->for($owner)->create([
        'title' => 'Secret',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs(User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]));

    Livewire::test(PlanningBoard::class)
        ->assertDontSee('Secret')
        ->assertSee('La journée est libre.');

    Carbon::setTestNow();
});
