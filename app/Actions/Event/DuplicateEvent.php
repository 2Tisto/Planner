<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use Illuminate\Support\Carbon;

final class DuplicateEvent
{
    public function __invoke(Event $event, ?Carbon $starts = null, ?Carbon $ends = null): Event
    {
        $copy = $event->replicate();
        $copy->fill([
            'starts_at' => ($starts ?? $event->beginsAt())->copy()->addDay()->utc(),
            'ends_at' => ($ends ?? $event->finishesAt())->copy()->addDay()->utc(),
            'recurrence_frequency' => null,
            'recurrence_until' => null,
            'recurrence_interval' => 1,
        ]);
        $copy->save();

        return $copy;
    }
}
