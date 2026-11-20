<?php

use App\Models\Deadline;
use App\Models\ReminderSend;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

test('a reminder is mailed once', function () {
    Mail::fake();
    Carbon::setTestNow(Carbon::parse('2026-11-20 09:00', 'Africa/Porto-Novo'));

    $user = User::factory()->create(['timezone' => 'Africa/Porto-Novo']);
    Deadline::factory()->for($user)->create([
        'title' => 'DS Algo',
        'happens_at' => Carbon::parse('2026-11-20 18:00', 'Africa/Porto-Novo')->utc(),
    ]);

    $this->artisan('planner:remind')->assertSuccessful();
    $this->artisan('planner:remind')->assertSuccessful();

    Mail::assertSentCount(1);
    expect(ReminderSend::query()->where('user_id', $user->id)->count())->toBe(1);

    Carbon::setTestNow();
});
