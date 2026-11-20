<?php

declare(strict_types=1);

namespace App\Actions\Habit;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;

final class ToggleHabit
{
    public function __invoke(User $user, Habit $habit, string $date): void
    {
        $existing = HabitLog::query()
            ->where('habit_id', $habit->id)
            ->whereDate('logged_on', $date)
            ->first();

        if ($existing instanceof HabitLog) {
            $existing->delete();

            return;
        }

        HabitLog::query()->create([
            'habit_id' => $habit->id,
            'user_id' => $user->id,
            'logged_on' => $date,
        ]);
    }
}
