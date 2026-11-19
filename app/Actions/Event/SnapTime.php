<?php

declare(strict_types=1);

namespace App\Actions\Event;

use Illuminate\Support\Carbon;

final class SnapTime
{
    public function __invoke(Carbon $moment): Carbon
    {
        $snap = max(1, (int) config('planner.snap_minutes', 15));
        $total = ((int) $moment->format('G')) * 60 + (int) $moment->format('i');
        $snapped = (int) (round($total / $snap) * $snap);

        return $moment->copy()->startOfDay()->addMinutes($snapped);
    }
}
