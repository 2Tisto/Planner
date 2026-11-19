<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\EventException;
use Illuminate\Support\Carbon;

final class ChangeSeries
{
    public function __construct(private SnapTime $snap, private OccurrenceMoment $moment) {}

    public function move(Event $event, string $occurrence, string $date, string $starts): void
    {
        $timezone = $event->timezone !== '' ? $event->timezone : 'UTC';
        $anchor = $event->beginsAt()->timezone($timezone);
        $dropped = $this->snap->__invoke(Carbon::parse($date.' '.$starts, $timezone));
        $shift = ($dropped->dayOfWeek - $anchor->dayOfWeek + 7) % 7;
        $length = max(60, $event->finishesAt()->getTimestamp() - $event->beginsAt()->getTimestamp());
        $newStart = $anchor->copy()->addDays($shift)->setTime((int) $dropped->format('G'), (int) $dropped->format('i'));
        $until = $event->untilDate();

        if ($until !== null && $newStart->toDateString() > $until) {
            $until = $newStart->toDateString();
        }

        $event->fill([
            'starts_at' => $newStart->copy()->utc(),
            'ends_at' => $newStart->copy()->addSeconds($length)->utc(),
            'recurrence_until' => $until,
        ])->save();

        $this->release($event, $occurrence, true);
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

        $event->fill([
            'ends_at' => $event->beginsAt()->copy()->addSeconds($end->getTimestamp() - $start->getTimestamp())->utc(),
        ])->save();

        $this->release($event, $occurrence, false);
    }

    private function release(Event $event, string $occurrence, bool $startToo): void
    {
        $exception = EventException::query()
            ->where('event_id', $event->id)
            ->whereDate('original_date', $occurrence)
            ->first();

        if (! $exception instanceof EventException) {
            return;
        }

        $cleared = ['ends_at' => null];

        if ($startToo) {
            $cleared['starts_at'] = null;
        }

        $exception->fill($cleared);

        if ($exception->type === 'overridden' && $this->blank($exception)) {
            $exception->delete();

            return;
        }

        $exception->save();
    }

    private function blank(EventException $exception): bool
    {
        return $exception->beginsAt() === null
            && $exception->finishesAt() === null
            && ($exception->title === null || $exception->title === '')
            && $exception->location === null
            && $exception->notes === null
            && $exception->category_id === null
            && $exception->subject_id === null;
    }
}
