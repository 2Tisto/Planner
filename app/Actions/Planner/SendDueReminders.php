<?php

declare(strict_types=1);

namespace App\Actions\Planner;

use App\Actions\Event\ExpandWeek;
use App\Mail\DueReminder;
use App\Models\Deadline;
use App\Models\ReminderSend;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

final class SendDueReminders
{
    public function __construct(private ExpandWeek $expand) {}

    public function __invoke(): int
    {
        $sent = 0;

        User::query()->orderBy('id')->each(function (User $user) use (&$sent): void {
            $sent += $this->forUser($user);
        });

        return $sent;
    }

    private function forUser(User $user): int
    {
        if ($user->email === '') {
            return 0;
        }

        $timezone = $user->timezone !== '' ? $user->timezone : 'Africa/Porto-Novo';
        $now = Carbon::now($timezone);
        $until = $now->copy()->addDay();
        $sent = 0;

        $deadlines = Deadline::query()
            ->forUser($user)
            ->where('happens_at', '>=', $now->copy()->utc())
            ->where('happens_at', '<', $until->copy()->utc())
            ->orderBy('happens_at')
            ->get();

        foreach ($deadlines as $deadline) {
            $when = $deadline->happensAt()->timezone($timezone)->format('d/m H:i');

            if ($this->deliver($user, 'deadline:'.$deadline->id, $deadline->title, $when)) {
                $sent++;
            }
        }

        foreach (($this->expand)($user, $now->copy()->startOfDay(), $until->copy()->endOfDay()) as $occurrence) {
            if ($occurrence->starts->lessThanOrEqualTo($now) || $occurrence->starts->greaterThan($until)) {
                continue;
            }

            $when = $occurrence->starts->copy()->timezone($timezone)->format('d/m H:i');

            if ($this->deliver($user, 'event:'.$occurrence->id.':'.$occurrence->date, $occurrence->title, $when)) {
                $sent++;
            }
        }

        return $sent;
    }

    private function deliver(User $user, string $key, string $title, string $when): bool
    {
        $already = ReminderSend::query()
            ->where('user_id', $user->id)
            ->where('subject_key', $key)
            ->exists();

        if ($already) {
            return false;
        }

        $locale = $user->locale !== '' ? $user->locale : 'fr';
        app()->setLocale($locale);

        Mail::to($user)->send(new DueReminder($title, $when));

        ReminderSend::query()->create([
            'user_id' => $user->id,
            'subject_key' => $key,
            'sent_at' => Carbon::now(),
        ]);

        return true;
    }
}
