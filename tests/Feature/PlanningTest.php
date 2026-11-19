<?php

use App\Livewire\Planning\PlanningBoard;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('opening the planner prepares the default colors', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class);

    expect($user->categories()->count())->toBe(12);
});

test('a student can place a block on the day', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);

    $this->actingAs($user);

    $today = now()->timezone('Africa/Porto-Novo')->toDateString();

    Livewire::test(PlanningBoard::class)
        ->call('openCreate', $today, '14:00')
        ->set('title', 'Réseaux')
        ->set('subjectName', 'Réseaux')
        ->set('location', 'Salle B12')
        ->set('ends', '16:00')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Réseaux');

    $event = Event::query()->where('user_id', $user->id)->first();

    expect($event)->not->toBeNull()
        ->and($event->starts_at->utc()->format('H:i'))->toBe('13:00')
        ->and($event->ends_at->utc()->format('H:i'))->toBe('15:00')
        ->and($event->subject->name)->toBe('Réseaux')
        ->and($event->location)->toBe('Salle B12');
});

test('a block that passes midnight keeps its length', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('openCreate', '2026-11-19', '23:00')
        ->set('title', 'Sommeil')
        ->set('ends', '01:00')
        ->call('save')
        ->assertHasNoErrors();

    $event = Event::query()->where('title', 'Sommeil')->firstOrFail();

    expect($event->starts_at->utc()->format('Y-m-d H:i'))->toBe('2026-11-19 22:00')
        ->and($event->ends_at->utc()->format('Y-m-d H:i'))->toBe('2026-11-20 00:00');
});

test('another student cannot open or see a block', function () {
    $owner = User::factory()->create();
    $event = Event::factory()->for($owner)->create([
        'title' => 'Secret',
    ]);
    $intruder = User::factory()->create();

    $this->actingAs($intruder)
        ->get(route('planning.board'))
        ->assertOk()
        ->assertDontSee('Secret');

    Livewire::test(PlanningBoard::class)
        ->call('openEdit', $event->id)
        ->assertNotFound();

    expect($event->refresh()->title)->toBe('Secret');
});

test('a block can be moved, resized and duplicated', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    $event = Event::factory()->for($user)->create([
        'title' => 'TD',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('moveBlock', $event->id, '2026-11-19', '2026-11-19', '15:10')
        ->assertHasNoErrors();

    $event->refresh();

    expect($event->starts_at->timezone('Africa/Porto-Novo')->format('H:i'))->toBe('15:15')
        ->and($event->ends_at->timezone('Africa/Porto-Novo')->format('H:i'))->toBe('17:15');

    Livewire::test(PlanningBoard::class)
        ->call('resizeBlock', $event->id, '2026-11-19', '16:00')
        ->assertHasNoErrors();

    expect($event->refresh()->ends_at->timezone('Africa/Porto-Novo')->format('H:i'))->toBe('16:00');

    Livewire::test(PlanningBoard::class)
        ->call('openEdit', $event->id)
        ->call('duplicate')
        ->assertHasNoErrors();

    $copy = Event::query()->where('title', 'TD')->whereKeyNot($event->id)->first();

    expect($copy)->not->toBeNull()
        ->and($copy->starts_at->timezone('Africa/Porto-Novo')->format('Y-m-d H:i'))->toBe('2026-11-20 15:15');
});

test('another student cannot move a block', function () {
    $owner = User::factory()->create();
    $event = Event::factory()->for($owner)->create([
        'title' => 'Secret',
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test(PlanningBoard::class)
        ->call('moveBlock', $event->id, '2026-11-19', '2026-11-19', '10:00')
        ->assertNotFound();

    expect($event->refresh()->title)->toBe('Secret');
});
