<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\EventException;
use Illuminate\Support\Carbon;

final class ChangeOccurrence
{
    public function __construct(private SnapTime $snap, private OccurrenceMoment $moment) {}

    public function move(Event $event, string $occurrence, string $date, string $starts): void
    {
        $current = $this->moment->__invoke($event, $occurrence);

        if ($current === null) {
            abort(404);
        }

        $timezone = $event->timezone !== '' ? $event->timezone : 'UTC';
        $minutes = max(1, (int) round(($current['ends']->getTimestamp() - $current['starts']->getTimestamp()) / 60));
        $start = $this->snap->__invoke(Carbon::parse($date.' '.$starts, $timezone));
        $this->keep($event, $occurrence, $start, $start->copy()->addMinutes($minutes));
    }

    public function resize(Event $event, string $occurrence, string $ends): void
    {
        $current = $this->moment->__invoke($event, $occurrence);

        if ($current === null) {
            abort(404);
        }

        $timezone = $event->timezone !== '' ? $event->timezone : 'UTC';
        $start = $current['starts']->copy()->timezone($timezone);
        $minimum = max(1, (int) config('planner.snap_minutes', 15));
        $end = $this->snap->__invoke(Carbon::parse($start->toDateString().' '.$ends, $timezone));

        if ($end->getTimestamp() - $start->getTimestamp() < $minimum * 60) {
            $end = $start->copy()->addMinutes($minimum);
        }

        $this->keep($event, $occurrence, $start, $end);
    }

    private function keep(Event $event, string $occurrence, Carbon $start, Carbon $end): void
    {
        $exception = EventException::remember($event, $occurrence);
        $exception->fill([
            'user_id' => $event->user_id,
            'type' => 'overridden',
            'starts_at' => $start->copy()->utc(),
            'ends_at' => $end->copy()->utc(),
        ])->save();
    }
}
