<?php

use App\Livewire\Deadline\DeadlineBoard;
use App\Models\Deadline;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('a deadline shows how many days remain', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-20 09:00', 'Africa/Porto-Novo'));

    $user = User::factory()->create(['timezone' => 'Africa/Porto-Novo']);
    $other = User::factory()->create();
    $secret = Deadline::factory()->for($other)->create(['title' => 'Secret']);

    $this->actingAs($user);

    Livewire::test(DeadlineBoard::class)
        ->set('title', 'DS Algo')
        ->set('kind', 'ds')
        ->set('date', '2026-11-23')
        ->set('time', '10:00')
        ->set('location', 'Amphi A')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('DS Algo')
        ->assertSee('Dans 3 jours')
        ->assertDontSee('Secret');

    $deadline = Deadline::query()->where('title', 'DS Algo')->firstOrFail();

    Livewire::test(DeadlineBoard::class)
        ->set('grades.'.$deadline->id, '14')
        ->call('saveGrade', $deadline->id)
        ->call('remove', $secret->id)
        ->assertNotFound();

    expect($deadline->refresh()->grade)->toBe('14.00');

    Carbon::setTestNow();
});
