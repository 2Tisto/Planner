<?php

use App\Actions\Event\ExpandWeek;
use App\Livewire\Planning\PlanningBoard;
use App\Models\Event;
use App\Models\EventException;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('a weekly block returns on that weekday until its end date', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    Event::factory()->for($user)->create([
        'title' => 'TD',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
        'recurrence_frequency' => 'weekly',
        'recurrence_interval' => 1,
        'recurrence_until' => '2026-12-03',
    ]);

    $expand = app(ExpandWeek::class);
    $dates = function (string $start, string $end) use ($expand, $user): array {
        return array_map(
            fn ($occurrence) => $occurrence->date,
            $expand($user, Carbon::parse($start, 'Africa/Porto-Novo'), Carbon::parse($end, 'Africa/Porto-Novo')),
        );
    };

    expect($dates('2026-11-16', '2026-11-23'))->toBe(['2026-11-19'])
        ->and($dates('2026-11-23', '2026-11-30'))->toBe(['2026-11-26'])
        ->and($dates('2026-12-07', '2026-12-14'))->toBe([]);
});

test('a skipped week and a cancelled day stay off the board', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    $event = Event::factory()->for($user)->create([
        'title' => 'TP',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
        'recurrence_frequency' => 'weekly',
        'recurrence_interval' => 2,
        'recurrence_until' => '2026-12-17',
    ]);
    EventException::query()->create([
        'event_id' => $event->id,
        'user_id' => $user->id,
        'original_date' => '2026-12-03',
        'type' => 'cancelled',
    ]);

    $dates = array_map(
        fn ($occurrence) => $occurrence->date,
        app(ExpandWeek::class)($user, Carbon::parse('2026-11-16', 'Africa/Porto-Novo'), Carbon::parse('2026-12-21', 'Africa/Porto-Novo')),
    );

    expect($dates)->toBe(['2026-11-19', '2026-12-17']);
});

test('one moved occurrence keeps the rest of the series in place', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    $event = Event::factory()->for($user)->create([
        'title' => 'Cours',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
        'recurrence_frequency' => 'weekly',
        'recurrence_until' => '2026-12-31',
    ]);
    EventException::query()->create([
        'event_id' => $event->id,
        'user_id' => $user->id,
        'original_date' => '2026-11-26',
        'type' => 'overridden',
        'starts_at' => Carbon::parse('2026-11-26 10:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-26 12:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $found = collect(app(ExpandWeek::class)(
        $user,
        Carbon::parse('2026-11-23', 'Africa/Porto-Novo'),
        Carbon::parse('2026-11-30', 'Africa/Porto-Novo'),
    ))->first();

    expect($found)->not->toBeNull()
        ->and($found->starts->timezone('Africa/Porto-Novo')->format('H:i'))->toBe('10:00')
        ->and($event->refresh()->beginsAt()->timezone('Africa/Porto-Novo')->format('H:i'))->toBe('14:00');
});

test('a student can repeat a block every week until a chosen day', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('openCreate', '2026-11-19', '14:00')
        ->set('title', 'Réseaux')
        ->set('repeats', true)
        ->set('until', '')
        ->call('save')
        ->assertHasErrors(['until']);

    Livewire::test(PlanningBoard::class)
        ->call('openCreate', '2026-11-19', '14:00')
        ->set('title', 'Réseaux')
        ->set('repeats', true)
        ->set('until', '2026-12-03')
        ->call('save')
        ->assertHasNoErrors()
        ->set('selectedDate', '2026-11-26')
        ->assertSee('Réseaux');

    $event = Event::query()->where('title', 'Réseaux')->firstOrFail();

    expect($event->recurrence_frequency)->toBe('weekly')
        ->and($event->untilDate())->toBe('2026-12-03');
});

test('changing one occurrence leaves the series title untouched', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    $event = Event::factory()->for($user)->create([
        'title' => 'TD',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
        'recurrence_frequency' => 'weekly',
        'recurrence_until' => '2026-12-31',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('openEdit', $event->id, '2026-11-26')
        ->set('title', 'Examen')
        ->call('save')
        ->assertHasNoErrors()
        ->set('selectedDate', '2026-11-19')
        ->assertSee('TD')
        ->assertDontSee('Examen')
        ->set('selectedDate', '2026-11-26')
        ->assertSee('Examen');

    expect($event->refresh()->title)->toBe('TD');
});

test('a series can be renamed, shortened, or dropped for a single day', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    $event = Event::factory()->for($user)->create([
        'title' => 'TD',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
        'recurrence_frequency' => 'weekly',
        'recurrence_until' => '2026-12-31',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('openEdit', $event->id, '2026-11-19')
        ->set('scope', 'series')
        ->set('title', 'Cours')
        ->set('until', '2026-12-03')
        ->call('save')
        ->assertHasNoErrors();

    expect($event->refresh()->title)->toBe('Cours')
        ->and($event->untilDate())->toBe('2026-12-03');

    Livewire::test(PlanningBoard::class)
        ->call('openEdit', $event->id, '2026-11-26')
        ->call('remove')
        ->assertHasNoErrors()
        ->set('selectedDate', '2026-11-26')
        ->assertDontSee('Cours')
        ->set('selectedDate', '2026-11-19')
        ->assertSee('Cours');

    expect(EventException::query()->where('event_id', $event->id)->value('type'))->toBe('cancelled');
});

test('dragging a repeated block asks before it moves one day or the series', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    $event = Event::factory()->for($user)->create([
        'title' => 'TD',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
        'recurrence_frequency' => 'weekly',
        'recurrence_until' => '2026-12-31',
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->call('moveBlock', $event->id, '2026-11-19', '2026-11-19', '15:00')
        ->assertSet('pendingKind', 'move')
        ->call('applyPending', 'once')
        ->assertSet('pendingKind', '');

    expect($event->refresh()->beginsAt()->timezone('Africa/Porto-Novo')->format('H:i'))->toBe('14:00');

    $moved = EventException::query()->where('event_id', $event->id)->first();

    expect($moved)->not->toBeNull()
        ->and($moved->beginsAt()->timezone('Africa/Porto-Novo')->format('H:i'))->toBe('15:00');

    Livewire::test(PlanningBoard::class)
        ->call('moveBlock', $event->id, '2026-11-26', '2026-11-27', '09:00')
        ->call('applyPending', 'series');

    expect($event->refresh()->beginsAt()->timezone('Africa/Porto-Novo')->format('Y-m-d H:i'))->toBe('2026-11-20 09:00');
});
