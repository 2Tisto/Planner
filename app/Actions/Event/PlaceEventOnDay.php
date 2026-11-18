<?php

declare(strict_types=1);

namespace App\Actions\Event;

use DateTimeInterface;

final class PlaceEventOnDay
{
    /**
     * Place a block on a day column. Each half hour is 3rem tall.
     *
     * @return array{top: float, height: float}|null
     */
    public function __invoke(
        DateTimeInterface $startsAt,
        DateTimeInterface $endsAt,
        DateTimeInterface $windowStart,
        DateTimeInterface $windowEnd,
    ): ?array {
        $start = max($startsAt->getTimestamp(), $windowStart->getTimestamp());
        $end = min($endsAt->getTimestamp(), $windowEnd->getTimestamp());

        if ($end <= $start) {
            return null;
        }

        $offsetMinutes = ($start - $windowStart->getTimestamp()) / 60;
        $durationMinutes = ($end - $start) / 60;

        return [
            'top' => (float) ($offsetMinutes / 30 * 3),
            'height' => (float) (max($durationMinutes, 25) / 30 * 3),
        ];
    }
}
