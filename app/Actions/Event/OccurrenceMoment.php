<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\EventException;
use Illuminate\Support\Carbon;

final class OccurrenceMoment
{
    /**
     * @return array{
     *     starts: Carbon,
     *     ends: Carbon,
     *     title: string,
     *     location: string,
     *     subject: string,
     *     categoryId: int|null,
     *     color: string|null
     * }|null
     */
    public function __invoke(Event $event, string $date): ?array
    {
        if (! $event->repeatsWeekly() || ! $this->occurs($event, $date)) {
            return null;
        }

        $exception = $this->exceptionFor($event, $date);

        if ($exception instanceof EventException && $exception->type === 'cancelled') {
            return null;
        }

        $timezone = $this->timezone($event);
        $clock = $event->beginsAt()->timezone($timezone);
        $start = Carbon::parse($date.' '.$clock->format('H:i:s'), $timezone);
        $length = max(60, $event->finishesAt()->getTimestamp() - $event->beginsAt()->getTimestamp());
        $end = $start->copy()->addSeconds($length);

        if ($exception instanceof EventException && $exception->beginsAt() instanceof Carbon) {
            $start = $exception->beginsAt();
            $end = $exception->finishesAt() ?? $start->copy()->addSeconds($length);
        } elseif ($exception instanceof EventException && $exception->finishesAt() instanceof Carbon) {
            $end = $exception->finishesAt();
        }

        $title = $event->title;
        $location = (string) ($event->location ?? '');
        $subject = $event->subjectLabel();
        $categoryId = $event->category_id;
        $color = $event->category?->color;

        if ($exception instanceof EventException) {
            if (is_string($exception->title) && $exception->title !== '') {
                $title = $exception->title;
            }

            if ($exception->location !== null) {
                $location = (string) $exception->location;
            }

            if ($exception->subject_id !== null) {
                $subject = $exception->subjectLabel();
            }

            if ($exception->category_id !== null) {
                $categoryId = $exception->category_id;
                $color = $exception->category?->color;
            }
        }

        return [
            'starts' => $start,
            'ends' => $end,
            'title' => $title,
            'location' => $location,
            'subject' => $subject,
            'categoryId' => is_numeric($categoryId) ? (int) $categoryId : null,
            'color' => is_string($color) ? $color : null,
        ];
    }

    private function occurs(Event $event, string $date): bool
    {
        $timezone = $this->timezone($event);
        $anchor = $event->beginsAt()->timezone($timezone)->startOfDay();
        $candidate = Carbon::parse($date, $timezone)->startOfDay();

        if ($candidate->dayOfWeek !== $anchor->dayOfWeek || $candidate->lt($anchor)) {
            return false;
        }

        $until = $event->untilDate();

        if ($until === null || $candidate->toDateString() > $until) {
            return false;
        }

        $weeks = intdiv((int) $anchor->diff($candidate)->format('%a'), 7);
        $interval = max(1, (int) $event->recurrence_interval);

        return $weeks % $interval === 0;
    }

    private function exceptionFor(Event $event, string $date): ?EventException
    {
        if ($event->relationLoaded('exceptions')) {
            foreach ($event->exceptions as $exception) {
                if ($exception->originalDate() === $date) {
                    return $exception;
                }
            }

            return null;
        }

        $found = $event->exceptions()->whereDate('original_date', $date)->first();

        return $found instanceof EventException ? $found : null;
    }

    private function timezone(Event $event): string
    {
        if ($event->timezone !== '') {
            return $event->timezone;
        }

        return 'UTC';
    }
}
