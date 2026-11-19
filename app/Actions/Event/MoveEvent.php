<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use Illuminate\Support\Carbon;

final class MoveEvent
{
    public function __construct(private SnapTime $snap) {}

    public function __invoke(Event $event, string $date, string $starts): void
    {
        $timezone = $event->timezone;
        $duration = (int) round(($event->finishesAt()->getTimestamp() - $event->beginsAt()->getTimestamp()) / 60);
        $start = $this->snap->__invoke(Carbon::parse($date.' '.$starts, $timezone));
        $end = $start->copy()->addMinutes(max($duration, 1));

        $event->fill([
            'starts_at' => $start->utc(),
            'ends_at' => $end->utc(),
        ])->save();
    }
}
