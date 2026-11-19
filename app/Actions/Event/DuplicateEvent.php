<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;

final class DuplicateEvent
{
    public function __invoke(Event $event): Event
    {
        $copy = $event->replicate();
        $copy->fill([
            'starts_at' => $event->beginsAt()->copy()->addDay(),
            'ends_at' => $event->finishesAt()->copy()->addDay(),
            'recurrence_frequency' => null,
            'recurrence_until' => null,
            'recurrence_interval' => 1,
        ]);
        $copy->save();

        return $copy;
    }
}
