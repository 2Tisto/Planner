<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;

final class DeleteEvent
{
    public function __invoke(Event $event): void
    {
        $event->delete();
    }
}
