<?php

use App\Livewire\Planning\PlanningBoard;
use App\Models\Event;
use App\Models\User;
use Livewire\Livewire;

test('a phrase fills the form and waits to be placed', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);

    $this->actingAs($user);

    $board = Livewire::test(PlanningBoard::class)
        ->set('selectedDate', '2026-11-19')
        ->set('phrase', 'TP réseaux mardi 14h-17h')
        ->call('draft')
        ->assertSet('composerOpen', true)
        ->assertSet('title', 'TP Réseaux')
        ->assertSet('subjectName', 'Réseaux')
        ->assertSet('starts', '14:00')
        ->assertSet('ends', '17:00')
        ->assertSet('selectedDate', '2026-11-24')
        ->assertSet('repeats', false)
        ->assertSet('phrase', '');

    $category = $user->categories()->where('name', 'TD/TP')->firstOrFail();

    $board->assertSet('categoryId', $category->id);

    expect(Event::query()->count())->toBe(0);

    $board->call('save')->assertHasNoErrors();

    $event = Event::query()->where('user_id', $user->id)->firstOrFail();

    expect($event->title)->toBe('TP Réseaux')
        ->and($event->subject->name)->toBe('Réseaux')
        ->and($event->starts_at->timezone('Africa/Porto-Novo')->format('Y-m-d H:i'))->toBe('2026-11-24 14:00')
        ->and($event->ends_at->timezone('Africa/Porto-Novo')->format('Y-m-d H:i'))->toBe('2026-11-24 17:00')
        ->and($event->category_id)->toBe($category->id);
});

test('a weekly phrase proposes an end date without saving yet', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->set('selectedDate', '2026-11-19')
        ->set('phrase', 'Anglais jeudi 9h30-10h30 chaque semaine')
        ->call('draft')
        ->assertSet('selectedDate', '2026-11-19')
        ->assertSet('starts', '09:30')
        ->assertSet('ends', '10:30')
        ->assertSet('repeats', true)
        ->assertSet('until', '2027-03-11');

    expect(Event::query()->count())->toBe(0);
});

test('a blank phrase leaves the board alone', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->set('phrase', '   ')
        ->call('draft')
        ->assertSet('composerOpen', false);
});
