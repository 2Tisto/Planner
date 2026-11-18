<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\User;

final class UpdateEvent
{
    public function __construct(private EventAttributes $attributes) {}

    /**
     * @param  array{
     *     title: string,
     *     date: string,
     *     starts: string,
     *     ends: string,
     *     category_id: int|null,
     *     subject_name: string|null,
     *     location: string|null
     * }  $attributes
     */
    public function __invoke(Event $event, User $user, array $attributes): void
    {
        $event->fill($this->attributes->__invoke($user, $attributes))->save();
    }
}
