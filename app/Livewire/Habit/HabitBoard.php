<?php

declare(strict_types=1);

namespace App\Livewire\Habit;

use App\Actions\Habit\ToggleHabit;
use App\Livewire\Concerns\ResolvesUser;
use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class HabitBoard extends Component
{
    use ResolvesUser;

    public string $name = '';

    public function save(): void
    {
        $user = $this->user();
        $this->authorize('create', Habit::class);

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('habits', 'name')->where(fn ($query) => $query->where('user_id', $user->id)),
            ],
        ]);

        Habit::query()->create([
            'user_id' => $user->id,
            'name' => $validated['name'],
        ]);

        $this->reset('name');
    }

    public function toggle(int $habitId, string $date, ToggleHabit $toggle): void
    {
        $habit = $this->owned($habitId);
        $this->authorize('update', $habit);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) !== 1) {
            abort(404);
        }

        $toggle($this->user(), $habit, $date);
    }

    public function remove(int $habitId): void
    {
        $habit = $this->owned($habitId);
        $this->authorize('delete', $habit);
        $habit->delete();
    }

    public function render(): View
    {
        $today = Carbon::now($this->timezone($this->user()))->startOfDay();
        $week = $today->copy()->startOfWeek(Carbon::MONDAY);
        $habits = Habit::query()
            ->forUser($this->user())
            ->with(['logs' => function ($query) use ($today): void {
                $query->where('logged_on', '>=', $today->copy()->subDays(60)->toDateString());
            }])
            ->orderBy('name')
            ->get();
        $rows = [];

        foreach ($habits as $habit) {
            $logged = [];

            foreach ($habit->logs as $log) {
                $logged[$this->day($log)] = true;
            }

            $days = [];

            for ($index = 0; $index < 7; $index++) {
                $date = $week->copy()->addDays($index);
                $key = $date->toDateString();
                $days[] = [
                    'date' => $key,
                    'label' => $this->label($date),
                    'done' => isset($logged[$key]),
                ];
            }

            $rows[] = [
                'id' => $habit->id,
                'name' => $habit->name,
                'streak' => $this->streak($logged, $today),
                'days' => $days,
            ];
        }

        return view('livewire.habit.habit-board', ['rows' => $rows]);
    }

    private function owned(int $habitId): Habit
    {
        $habit = Habit::query()->forUser($this->user())->find($habitId);

        if (! $habit instanceof Habit) {
            abort(404);
        }

        return $habit;
    }

    private function day(HabitLog $log): string
    {
        return $log->loggedOn();
    }

    /**
     * @param  array<string, true>  $logged
     */
    private function streak(array $logged, Carbon $today): int
    {
        $cursor = $today->copy();

        if (! isset($logged[$cursor->toDateString()])) {
            $cursor->subDay();
        }

        $count = 0;

        while (isset($logged[$cursor->toDateString()])) {
            $count++;
            $cursor->subDay();
        }

        return $count;
    }

    private function label(Carbon $date): string
    {
        return match ($date->dayOfWeekIso) {
            1 => __('Mo'),
            2 => __('Tu'),
            3 => __('We'),
            4 => __('Th'),
            5 => __('Fr'),
            6 => __('Sa'),
            default => __('Su'),
        };
    }
}
