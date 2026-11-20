<?php

declare(strict_types=1);

namespace App\Livewire\Home;

use App\Actions\Planner\DescribeNow;
use App\Actions\Planner\SuggestFreeHour;
use App\Actions\Share\OpenShare;
use App\Actions\Study\RecordStudy;
use App\Enums\TaskStatus;
use App\Livewire\Concerns\ResolvesUser;
use App\Models\Deadline;
use App\Models\Share;
use App\Models\StudySession;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class HomeBoard extends Component
{
    use ResolvesUser;

    public string $shareUrl = '';

    public function mount(): void
    {
        $share = Share::query()->where('user_id', $this->user()->id)->first();

        if ($share instanceof Share) {
            $this->shareUrl = route('share.show', $share->token);
        }
    }

    public function note(RecordStudy $record): void
    {
        $record($this->user(), null, 25);
    }

    public function share(OpenShare $open): void
    {
        $share = $open($this->user());
        $this->shareUrl = route('share.show', $share->token);
    }

    public function render(DescribeNow $describe, SuggestFreeHour $suggest): View
    {
        $user = $this->user();
        $timezone = $this->timezone($user);
        $now = Carbon::now($timezone);
        $today = $now->copy()->startOfDay();
        $focus = $describe($user);
        $tasks = Task::query()->forUser($user)->orderBy('id')->get();
        $doing = $tasks
            ->filter(fn (Task $task): bool => $task->status() === TaskStatus::Doing)
            ->values();
        $priorities = $tasks
            ->filter(fn (Task $task): bool => $task->status() !== TaskStatus::Done)
            ->sortBy(fn (Task $task): string => $this->rank($task))
            ->take(3)
            ->values();
        $overdue = $tasks
            ->filter(fn (Task $task): bool => $this->late($task, $today))
            ->values();
        $deadlines = Deadline::query()
            ->forUser($user)
            ->where('happens_at', '>=', $now->copy()->utc())
            ->orderBy('happens_at')
            ->limit(3)
            ->get();
        $coming = [];

        foreach ($deadlines as $deadline) {
            $when = $deadline->happensAt()->timezone($timezone);
            $days = intdiv($when->copy()->startOfDay()->getTimestamp() - $today->getTimestamp(), 86400);
            $coming[] = [
                'title' => $deadline->title,
                'when' => $when->format('d/m H:i'),
                'countdown' => $this->countdown($days),
            ];
        }

        $soon = Deadline::query()
            ->forUser($user)
            ->where('happens_at', '>=', $now->copy()->utc())
            ->where('happens_at', '<', $now->copy()->addDays(14)->utc())
            ->exists();
        $free = $soon ? $suggest($user, $today) : null;
        $actual = (int) StudySession::query()
            ->forUser($user)
            ->where('started_at', '>=', $today->copy()->utc())
            ->where('started_at', '<', $today->copy()->addDay()->utc())
            ->sum('minutes');

        return view('livewire.home.home-board', [
            'label' => $now->isoFormat('dddd D MMMM'),
            'focus' => $focus,
            'doing' => $doing,
            'priorities' => $priorities,
            'overdue' => $overdue,
            'coming' => $coming,
            'free' => $free,
            'planned' => $this->hours($focus['planned']),
            'actual' => $this->hours($actual),
        ]);
    }

    private function rank(Task $task): string
    {
        $order = ['A' => '0', 'B' => '1', 'C' => '2'];
        $due = $task->dueOn();
        $date = $due instanceof Carbon ? $due->toDateString() : '9999-99-99';

        return $order[$task->priority()->value].'-'.$date.'-'.sprintf('%08d', $task->id);
    }

    private function late(Task $task, Carbon $today): bool
    {
        if ($task->status() === TaskStatus::Done) {
            return false;
        }

        $due = $task->dueOn();

        if (! $due instanceof Carbon) {
            return false;
        }

        return $due->toDateString() < $today->toDateString();
    }

    private function countdown(int $days): string
    {
        if ($days <= 0) {
            return __('Today');
        }

        if ($days === 1) {
            return __('Tomorrow');
        }

        return __('In :count days', ['count' => $days]);
    }

    private function hours(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes.' min';
        }

        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;

        if ($rest === 0) {
            return $hours.' h';
        }

        return $hours.' h '.$rest;
    }
}
