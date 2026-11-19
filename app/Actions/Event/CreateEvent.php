<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\User;

final class CreateEvent
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
    public function __invoke(User $user, array $attributes, bool $repeats = false, ?string $until = null): Event
    {
        $values = $this->attributes->__invoke($user, $attributes);

        return Event::query()->create([
            ...$values,
            'user_id' => $user->id,
            'notes' => null,
            'is_all_day' => false,
            'recurrence_frequency' => $repeats ? 'weekly' : null,
            'recurrence_interval' => 1,
            'recurrence_until' => $repeats ? $until : null,
        ]);
    }
}
