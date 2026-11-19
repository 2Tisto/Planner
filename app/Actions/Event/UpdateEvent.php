<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Carbon;

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
    public function __invoke(Event $event, User $user, array $attributes, bool $repeats = false, ?string $until = null): void
    {
        $values = $this->attributes->__invoke($user, $attributes);

        if ($event->repeatsWeekly()) {
            $timezone = $event->timezone !== '' ? $event->timezone : (string) config('planner.default_timezone');
            $anchor = $event->beginsAt()->timezone($timezone)->toDateString();
            $start = Carbon::parse($anchor.' '.$attributes['starts'], $timezone);
            $end = Carbon::parse($anchor.' '.$attributes['ends'], $timezone);

            if ($end->lessThanOrEqualTo($start)) {
                $end = $end->addDay();
            }

            $values['starts_at'] = $start->utc();
            $values['ends_at'] = $end->utc();
            $repeats = true;
            $until ??= $event->untilDate();
        }

        $event->fill([
            ...$values,
            'recurrence_frequency' => $repeats ? 'weekly' : null,
            'recurrence_interval' => 1,
            'recurrence_until' => $repeats ? $until : null,
        ])->save();
    }
}
