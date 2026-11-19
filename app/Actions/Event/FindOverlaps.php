<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Models\User;
use Illuminate\Support\Carbon;

final class FindOverlaps
{
    public function __construct(private ExpandWeek $expand) {}

    /**
     * Half-open ranges: a block that ends when the next one starts is not a clash.
     *
     * @return list<string>
     */
    public function __invoke(User $user, Carbon $start, Carbon $end, ?int $ignoreEventId = null, ?string $ignoreDate = null): array
    {
        $from = $start->copy()->startOfDay()->subDay();
        $until = $end->copy()->startOfDay()->addDays(2);
        $titles = [];

        foreach ($this->expand->__invoke($user, $from, $until) as $occurrence) {
            if ($ignoreEventId !== null && $occurrence->id === $ignoreEventId && $occurrence->date === $ignoreDate) {
                continue;
            }

            $begins = $occurrence->starts->getTimestamp();
            $finishes = $occurrence->ends->getTimestamp();

            if ($begins < $end->getTimestamp() && $finishes > $start->getTimestamp()) {
                $titles[$occurrence->title] = true;
            }
        }

        $list = [];

        foreach (array_keys($titles) as $title) {
            $list[] = (string) $title;
        }

        return $list;
    }
}
