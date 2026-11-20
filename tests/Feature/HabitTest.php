<?php

use App\Livewire\Habit\HabitBoard;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('tapping a day records the habit', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-20 09:00', 'Africa/Porto-Novo'));

    $user = User::factory()->create(['timezone' => 'Africa/Porto-Novo']);
    $this->actingAs($user);

    Livewire::test(HabitBoard::class)
        ->set('name', 'Réviser 30 min')
        ->call('save')
        ->assertHasNoErrors()
        ->call('toggle', Habit::query()->where('name', 'Réviser 30 min')->firstOrFail()->id, '2026-11-20')
        ->assertSee('Réviser 30 min');

    expect(HabitLog::query()->whereDate('logged_on', '2026-11-20')->exists())->toBeTrue();

    Carbon::setTestNow();
});
