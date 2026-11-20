<?php

declare(strict_types=1);

namespace App\Actions\Planner;

use App\Actions\Event\ExpandWeek;
use App\Models\User;
use Illuminate\Support\Carbon;

final class BuildCalendar
{
    public function __construct(private ExpandWeek $expand) {}

    public function __invoke(User $user): string
    {
        $timezone = $user->timezone !== '' ? $user->timezone : 'Africa/Porto-Novo';
        $from = Carbon::now($timezone)->startOfDay();
        $until = $from->copy()->addWeeks(16);
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Planner//FR',
            'CALSCALE:GREGORIAN',
        ];

        foreach (($this->expand)($user, $from, $until) as $occurrence) {
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:event-'.$occurrence->id.'-'.$occurrence->date.'@planner';
            $lines[] = 'DTSTAMP:'.Carbon::now()->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTSTART:'.$occurrence->starts->copy()->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTEND:'.$occurrence->ends->copy()->utc()->format('Ymd\THis\Z');
            $lines[] = 'SUMMARY:'.$this->text($occurrence->title);
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines)."\r\n";
    }

    private function text(string $value): string
    {
        return str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $value);
    }
}
