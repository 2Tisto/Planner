<?php

use App\Actions\Planner\SuggestFreeHour;
use App\Livewire\Home\HomeBoard;
use App\Models\Event;
use App\Models\Share;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('a guest sees only the shared week', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-20 09:00', 'Africa/Porto-Novo'));

    $user = User::factory()->create(['name' => 'Jean', 'timezone' => 'Africa/Porto-Novo']);
    $other = User::factory()->create();

    Event::factory()->for($user)->create([
        'title' => 'Réseaux',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-20 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-20 16:00', 'Africa/Porto-Novo')->utc(),
    ]);
    Event::factory()->for($other)->create([
        'title' => 'Secret',
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-20 14:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-20 15:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->actingAs($user);
    Livewire::test(HomeBoard::class)->call('share');
    auth()->logout();

    $token = Share::query()->where('user_id', $user->id)->firstOrFail()->token;

    $this->get(route('share.show', $token))
        ->assertOk()
        ->assertSee('Réseaux')
        ->assertDontSee('Secret');

    Carbon::setTestNow();
});

test('a free hour starts when the morning block ends', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-20 07:00', 'Africa/Porto-Novo'));

    $user = User::factory()->create(['timezone' => 'Africa/Porto-Novo']);
    Event::factory()->for($user)->create([
        'timezone' => 'Africa/Porto-Novo',
        'starts_at' => Carbon::parse('2026-11-20 08:00', 'Africa/Porto-Novo')->utc(),
        'ends_at' => Carbon::parse('2026-11-20 09:00', 'Africa/Porto-Novo')->utc(),
    ]);

    expect(app(SuggestFreeHour::class)($user, Carbon::parse('2026-11-20', 'Africa/Porto-Novo')))->toBe('09:00');

    Carbon::setTestNow();
});
