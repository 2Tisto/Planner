<?php

use App\Livewire\Planning\PlanningBoard;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('the month shows a block on its day and opens that day', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    Event::factory()->for($user)->create([
        'title' => 'Soutenance',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->set('selectedDate', '2026-11-19')
        ->call('showMonth')
        ->assertSet('span', 'month')
        ->assertSee('Soutenance')
        ->assertSee('novembre')
        ->assertSee('Touchez un jour pour l\'ouvrir.')
        ->call('showDay', '2026-11-19')
        ->assertSet('span', 'day')
        ->assertSet('selectedDate', '2026-11-19')
        ->assertSee('Soutenance');
});

test('a weekly block is listed once for each week of the month', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
        'week_starts_on' => 1,
    ]);
    Event::factory()->for($user)->create([
        'title' => 'Atelier',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-05 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-05 16:00', 'Africa/Porto-Novo')->utc(),
        'recurrence_frequency' => 'weekly',
        'recurrence_until' => '2026-11-30',
    ]);

    $this->actingAs($user);

    $html = Livewire::test(PlanningBoard::class)
        ->set('selectedDate', '2026-11-19')
        ->call('showMonth')
        ->html();

    expect(substr_count($html, 'Atelier'))->toBe(4);
});

test('the month arrows move a whole month', function () {
    $user = User::factory()->create([
        'timezone' => 'Africa/Porto-Novo',
    ]);
    Event::factory()->for($user)->create([
        'title' => 'Soutenance',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user);

    Livewire::test(PlanningBoard::class)
        ->set('selectedDate', '2026-11-19')
        ->call('showMonth')
        ->call('shiftDay', 1)
        ->assertSet('selectedDate', '2026-12-19')
        ->assertDontSee('Soutenance');
});

test('another student does not see a block on the month', function () {
    $owner = User::factory()->create();
    Event::factory()->for($owner)->create([
        'title' => 'Secret',
        'starts_at' => Carbon::parse('2026-11-19 14:00', 'UTC'),
        'ends_at' => Carbon::parse('2026-11-19 16:00', 'UTC'),
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test(PlanningBoard::class)
        ->set('selectedDate', '2026-11-19')
        ->call('showMonth')
        ->assertDontSee('Secret');
});
