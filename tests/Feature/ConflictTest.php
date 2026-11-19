<?php

use App\Livewire\Planning\PlanningBoard;
use App\Models\Event;
use App\Models\User;
use Livewire\Livewire;

test('a block that starts when another ends is not a clash', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('openCreate', '2026-11-19', '14:00')
        ->set('title', 'Cours')
        ->set('ends', '15:00')
        ->call('save')
        ->call('openCreate', '2026-11-19', '15:00')
        ->set('title', 'TD')
        ->set('ends', '16:00')
        ->assertDontSee('Ce créneau croise')
        ->call('save')
        ->assertHasNoErrors();

    expect(Event::query()->where('user_id', $user->id)->count())->toBe(2);
});

test('an overlap is mentioned and the block can still be placed', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('openCreate', '2026-11-19', '14:00')
        ->set('title', 'Cours')
        ->set('ends', '16:00')
        ->call('save')
        ->call('openCreate', '2026-11-19', '15:00')
        ->set('title', 'TD')
        ->set('ends', '17:00')
        ->assertSee('Ce créneau croise Cours')
        ->call('save')
        ->assertHasNoErrors();

    expect(Event::query()->where('user_id', $user->id)->count())->toBe(2);
});

test('editing a block does not warn about itself', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    $event = Event::factory()->for($user)->create([
        'title' => 'Cours',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => '2026-11-19 13:00:00',
        'ends_at' => '2026-11-19 15:00:00',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('openEdit', $event->id)
        ->assertDontSee('Ce créneau croise');
});
