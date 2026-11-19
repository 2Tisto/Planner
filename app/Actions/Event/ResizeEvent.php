<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use Illuminate\Support\Carbon;

final class ResizeEvent
{
    public function __construct(private SnapTime $snap) {}

    public function __invoke(Event $event, string $ends): void
    {
        $timezone = $event->timezone;
        $start = $event->beginsAt()->timezone($timezone);
        $minimum = max(1, (int) config('planner.snap_minutes', 15));
        $end = $this->snap->__invoke(Carbon::parse($start->toDateString().' '.$ends, $timezone));

        if ($end->getTimestamp() - $start->getTimestamp() < $minimum * 60) {
            $end = $start->copy()->addMinutes($minimum);
        }

        $event->fill([
            'ends_at' => $end->utc(),
        ])->save();
    }
}
