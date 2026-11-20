<?php

declare(strict_types=1);

namespace App\Actions\Planner;

use App\Actions\Event\ExpandWeek;
use App\Actions\Event\Occurrence;
use App\Models\User;
use Illuminate\Support\Carbon;

final class DescribeNow
{
    public function __construct(private ExpandWeek $expand) {}

    /**
     * @return array{
     *     current: array{id: int, date: string, title: string, time: string, meta: string, color: string}|null,
     *     next: array{id: int, date: string, title: string, time: string, meta: string, color: string}|null,
     *     quiet: 'open'|'waiting'|'clear'|null,
     *     today: list<array{title: string, time: string}>,
     *     planned: int
     * }
     */
    public function __invoke(User $user): array
    {
        $timezone = $this->timezone($user);
        $now = Carbon::now($timezone);
        $day = $now->copy()->startOfDay();
        $occurrences = ($this->expand)($user, $day, $day->copy()->addDay());
        $ongoing = [];
        $upcoming = [];
        $finished = false;
        $today = [];
        $planned = 0;

        foreach ($occurrences as $occurrence) {
            $planned += $this->minutes($occurrence);
            $today[] = [
                'title' => $occurrence->title,
                'time' => $this->span($occurrence, $timezone),
            ];

            if ($occurrence->starts->lessThanOrEqualTo($now) && $occurrence->ends->greaterThan($now)) {
                $ongoing[] = $occurrence;
            } elseif ($occurrence->starts->greaterThan($now)) {
                $upcoming[] = $occurrence;
            } elseif ($occurrence->ends->lessThanOrEqualTo($now)) {
                $finished = true;
            }
        }

        usort($ongoing, fn (Occurrence $left, Occurrence $right): int => $right->starts->getTimestamp() <=> $left->starts->getTimestamp());
        usort($upcoming, fn (Occurrence $left, Occurrence $right): int => $left->starts->getTimestamp() <=> $right->starts->getTimestamp());

        $current = $ongoing[0] ?? null;
        $next = $upcoming[0] ?? null;
        $quiet = null;

        if (! $current instanceof Occurrence) {
            $quiet = $next instanceof Occurrence ? 'waiting' : ($finished ? 'clear' : 'open');
        }

        return [
            'current' => $current instanceof Occurrence ? $this->present($current, $timezone) : null,
            'next' => $next instanceof Occurrence ? $this->present($next, $timezone) : null,
            'quiet' => $quiet,
            'today' => $today,
            'planned' => $planned,
        ];
    }

    private function timezone(User $user): string
    {
        if ($user->timezone !== '') {
            return $user->timezone;
        }

        $fallback = config('planner.default_timezone');

        return is_string($fallback) && $fallback !== '' ? $fallback : 'Africa/Porto-Novo';
    }

    private function minutes(Occurrence $occurrence): int
    {
        return max(0, (int) round(($occurrence->ends->getTimestamp() - $occurrence->starts->getTimestamp()) / 60));
    }

    private function span(Occurrence $occurrence, string $timezone): string
    {
        $starts = $occurrence->starts->copy()->timezone($timezone);
        $ends = $occurrence->ends->copy()->timezone($timezone);

        return $this->clock($starts).' – '.$this->clock($ends);
    }

    /**
     * @return array{id: int, date: string, title: string, time: string, meta: string, color: string}
     */
    private function present(Occurrence $occurrence, string $timezone): array
    {
        return [
            'id' => $occurrence->id,
            'date' => $occurrence->date,
            'title' => $occurrence->title,
            'time' => $this->span($occurrence, $timezone),
            'meta' => $occurrence->meta,
            'color' => $this->color($occurrence->color),
        ];
    }

    private function clock(Carbon $moment): string
    {
        if ($moment->minute === 0) {
            return $moment->format('G').'h';
        }

        return $moment->format('G').'h'.$moment->format('i');
    }

    private function color(?string $color): string
    {
        if ($color !== null && preg_match('/^#[0-9a-fA-F]{6}$/', $color) === 1) {
            return strtolower($color);
        }

        return '#e5e7eb';
    }
}
