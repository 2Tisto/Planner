<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\EventException;
use App\Models\User;

final class OverrideOccurrence
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
    public function __invoke(Event $event, User $user, string $occurrence, array $attributes): EventException
    {
        $values = $this->attributes->__invoke($user, $attributes);
        $exception = EventException::remember($event, $occurrence);
        $exception->fill([
            'user_id' => $user->id,
            'type' => 'overridden',
            'starts_at' => $values['starts_at'],
            'ends_at' => $values['ends_at'],
            'title' => $values['title'],
            'location' => $values['location'],
            'category_id' => $values['category_id'],
            'subject_id' => $values['subject_id'],
        ])->save();

        return $exception;
    }
}
