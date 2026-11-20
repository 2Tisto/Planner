<?php

use App\Livewire\Home\HomeBoard;
use App\Models\Deadline;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('the home shows the moment, a priority and a deadline', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-20 14:30', 'Africa/Porto-Novo'));

    $user = User::factory()->create(['timezone' => 'Africa/Porto-Novo']);
    $other = User::factory()->create();

    Event::factory()->for($user)->create([
        'title' => 'Réseaux',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-20 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-20 16:00', 'Africa/Porto-Novo')->utc(),
    ]);
    Task::factory()->for($user)->create(['title' => 'Fiche', 'priority' => 'A']);
    Task::factory()->for($user)->create(['title' => 'Mémoire', 'status' => 'doing']);
    Task::factory()->for($other)->create(['title' => 'Secret']);
    Deadline::factory()->for($user)->create([
        'title' => 'DS Algo',
        'happens_at' => Carbon::parse('2026-11-23 10:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user);

    Livewire::test(HomeBoard::class)
        ->assertSee('En ce moment')
        ->assertSee('Réseaux')
        ->assertSee('Fiche')
        ->assertSee('En cours')
        ->assertSee('Mémoire')
        ->assertSee('DS Algo')
        ->assertSee('Dans 3 jours')
        ->assertSee('Une heure libre')
        ->assertDontSee('Secret');

    $this->get(route('dashboard'))->assertOk()->assertSee('En ce moment');

    Carbon::setTestNow();
});
