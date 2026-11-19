<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\EventException;

final class CancelOccurrence
{
    public function __invoke(Event $event, string $occurrence): void
    {
        $exception = EventException::remember($event, $occurrence);
        $exception->fill([
            'user_id' => $event->user_id,
            'type' => 'cancelled',
            'starts_at' => null,
            'ends_at' => null,
            'title' => null,
            'location' => null,
            'notes' => null,
            'category_id' => null,
            'subject_id' => null,
        ])->save();
    }
}
