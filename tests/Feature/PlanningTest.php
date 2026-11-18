<?php

use App\Livewire\Planning\PlanningBoard;
use App\Models\Event;
use App\Models\User;
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
