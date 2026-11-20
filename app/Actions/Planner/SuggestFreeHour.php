<?php

declare(strict_types=1);

namespace App\Actions\Planner;

use App\Actions\Event\ExpandWeek;
use App\Models\User;
use Illuminate\Support\Carbon;

final class SuggestFreeHour
{
    public function __construct(private ExpandWeek $expand) {}

    public function __invoke(User $user, Carbon $day): ?string
    {
        $timezone = $user->timezone !== '' ? $user->timezone : 'Africa/Porto-Novo';
        $local = $day->copy()->timezone($timezone)->startOfDay();
        $occurrences = ($this->expand)($user, $local, $local->copy()->addDay());

        for ($minutes = 8 * 60; $minutes + 60 <= 20 * 60; $minutes += 30) {
            $start = $local->copy()->addMinutes($minutes);
            $finish = $start->copy()->addHour();
            $busy = false;

            foreach ($occurrences as $occurrence) {
                if ($occurrence->starts->lt($finish) && $occurrence->ends->gt($start)) {
                    $busy = true;
                    break;
                }
            }

            if (! $busy) {
                return $start->format('H:i');
            }
        }

        return null;
    }
}
