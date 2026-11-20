<?php

declare(strict_types=1);

namespace App\Actions\Review;

use App\Models\User;
use App\Models\WeeklyReview;

final class SaveReview
{
    public function __invoke(
        User $user,
        string $weekStart,
        ?string $wins,
        ?string $hard,
        ?string $nextStep,
        ?int $satisfaction,
    ): WeeklyReview {
        $review = WeeklyReview::query()
            ->forUser($user)
            ->whereDate('week_start', $weekStart)
            ->first();

        $payload = [
            'wins' => $wins,
            'hard' => $hard,
            'next_step' => $nextStep,
            'satisfaction' => $satisfaction,
        ];

        if ($review instanceof WeeklyReview) {
            $review->fill($payload);
            $review->save();

            return $review;
        }

        return WeeklyReview::query()->create([
            'user_id' => $user->id,
            'week_start' => $weekStart,
            ...$payload,
        ]);
    }
}
