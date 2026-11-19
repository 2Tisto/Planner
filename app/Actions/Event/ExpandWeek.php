<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Carbon;

final class ExpandWeek
{
    public function __construct(private OccurrenceMoment $moment) {}

    /**
     * @return list<Occurrence>
     */
    public function __invoke(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        $rangeStart = $weekStart->copy()->utc();
        $rangeEnd = $weekEnd->copy()->utc();

        $oneOffs = Event::query()
            ->forUser($user)
            ->whereNull('recurrence_frequency')
            ->where('starts_at', '<', $rangeEnd)
            ->where('ends_at', '>', $rangeStart)
            ->with(['category', 'subject'])
            ->orderBy('starts_at')
            ->get();

        $series = Event::query()
            ->forUser($user)
            ->where('recurrence_frequency', 'weekly')
            ->whereNotNull('recurrence_until')
            ->where(function ($query) use ($weekStart, $rangeStart, $rangeEnd): void {
                $query->where(function ($active) use ($weekStart, $rangeEnd): void {
                    $active->where('starts_at', '<', $rangeEnd)
                        ->whereDate('recurrence_until', '>=', $weekStart->toDateString());
                })->orWhereHas('exceptions', function ($exceptions) use ($rangeStart, $rangeEnd): void {
                    $exceptions->where('type', 'overridden')
                        ->where('starts_at', '<', $rangeEnd)
                        ->where('ends_at', '>', $rangeStart);
                });
            })
            ->with([
                'category',
                'subject',
                'exceptions' => function ($query) use ($weekStart, $weekEnd, $rangeStart, $rangeEnd): void {
                    $query->where(function ($inner) use ($weekStart, $weekEnd, $rangeStart, $rangeEnd): void {
                        $inner->whereBetween('original_date', [
                            $weekStart->copy()->subWeek()->toDateString(),
                            $weekEnd->toDateString(),
                        ])->orWhere(function ($moved) use ($rangeStart, $rangeEnd): void {
                            $moved->where('starts_at', '<', $rangeEnd)
                                ->where('ends_at', '>', $rangeStart);
                        });
                    })->with(['category', 'subject']);
                },
            ])
            ->orderBy('starts_at')
            ->get();

        $items = [];

        foreach ($oneOffs as $event) {
            $items[] = $this->present(
                $event,
                $event->beginsAt()->timezone($this->timezone($event))->toDateString(),
                $event->beginsAt(),
                $event->finishesAt(),
                $event->title,
                $this->meta($event->subjectLabel(), (string) ($event->location ?? '')),
                $event->category?->color,
                false,
            );
        }

        foreach ($series as $event) {
            foreach ($this->dates($event, $weekStart, $weekEnd) as $date) {
                $resolved = $this->moment->__invoke($event, $date);

                if ($resolved === null) {
                    continue;
                }

                if ($resolved['ends']->getTimestamp() <= $rangeStart->getTimestamp()) {
                    continue;
                }

                if ($resolved['starts']->getTimestamp() >= $rangeEnd->getTimestamp()) {
                    continue;
                }

                $items[] = $this->present(
                    $event,
                    $date,
                    $resolved['starts'],
                    $resolved['ends'],
                    $resolved['title'],
                    $this->meta($resolved['subject'], $resolved['location']),
                    $resolved['color'],
                    true,
                );
            }
        }

        return $items;
    }

    /**
     * @return list<string>
     */
    private function dates(Event $event, Carbon $weekStart, Carbon $weekEnd): array
    {
        $dates = [];

        for ($day = $weekStart->copy(); $day->lt($weekEnd); $day->addDay()) {
            $dates[$day->toDateString()] = true;
        }

        foreach ($event->exceptions as $exception) {
            if ($exception->originalDate() !== '') {
                $dates[$exception->originalDate()] = true;
            }
        }

        $list = [];

        foreach (array_keys($dates) as $date) {
            $list[] = (string) $date;
        }

        sort($list);

        return $list;
    }

    private function present(
        Event $event,
        string $date,
        Carbon $starts,
        Carbon $ends,
        string $title,
        string $meta,
        ?string $color,
        bool $recurring,
    ): Occurrence {
        return new Occurrence(
            $event->id,
            $date,
            $starts,
            $ends,
            $title,
            $meta,
            is_string($color) ? $color : null,
            $recurring,
        );
    }

    private function meta(string $subject, string $location): string
    {
        if ($subject !== '') {
            return $subject;
        }

        return $location;
    }

    private function timezone(Event $event): string
    {
        if ($event->timezone !== '') {
            return $event->timezone;
        }

        return 'UTC';
    }
}
