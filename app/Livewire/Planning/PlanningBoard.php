<?php

declare(strict_types=1);

namespace App\Livewire\Planning;

use App\Actions\Category\SeedDefaultCategories;
use App\Actions\Event\CreateEvent;
use App\Actions\Event\DeleteEvent;
use App\Actions\Event\PlaceEventOnDay;
use App\Actions\Event\UpdateEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PlanningBoard extends Component
{
    private const START_HOUR = 7;

    private const END_HOUR = 24;

    public string $selectedDate = '';

    public bool $composerOpen = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $subjectName = '';

    public string $location = '';

    public string $starts = '08:00';

    public string $ends = '09:00';

    public ?int $categoryId = null;

    public function mount(SeedDefaultCategories $seed): void
    {
        $user = $this->user();

        if ($user->categories()->doesntExist()) {
            $seed($user);
        }

        $this->selectedDate = Carbon::now($this->timezone($user))->toDateString();
        $firstCategory = $user->categories()->orderBy('position')->value('id');
        $this->categoryId = is_numeric($firstCategory) ? (int) $firstCategory : null;
    }

    public function selectDate(string $date): void
    {
        $parsed = Carbon::createFromFormat('Y-m-d', $date);

        if (! $parsed instanceof Carbon) {
            return;
        }

        $this->selectedDate = $parsed->toDateString();
        $this->closeComposer();
    }

    public function shiftDay(int $days): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDays($days)->toDateString();
        $this->closeComposer();
    }

    public function goToToday(): void
    {
        $this->selectedDate = Carbon::now($this->timezone($this->user()))->toDateString();
        $this->closeComposer();
    }

    public function openQuickAdd(): void
    {
        $moment = Carbon::now($this->timezone($this->user()));
        $hour = (int) $moment->format('G');
        $minute = (int) $moment->format('i') < 30 ? 30 : 0;

        if ((int) $moment->format('i') >= 30) {
            $hour++;
        }

        if ($hour < self::START_HOUR || $hour >= self::END_HOUR) {
            $hour = 8;
            $minute = 0;
        }

        $this->openCreate($moment->toDateString(), sprintf('%02d:%02d', $hour, $minute));
    }

    public function openCreate(string $date, string $starts): void
    {
        $parsed = Carbon::createFromFormat('Y-m-d', $date);
        $start = Carbon::createFromFormat('H:i', $starts);

        if (! $parsed instanceof Carbon || ! $start instanceof Carbon) {
            return;
        }

        $this->resetErrorBag();
        $this->editingId = null;
        $this->selectedDate = $parsed->toDateString();
        $this->title = '';
        $this->subjectName = '';
        $this->location = '';
        $this->starts = $start->format('H:i');
        $this->ends = $start->copy()->addHour()->format('H:i');
        $this->composerOpen = true;
    }

    public function openEdit(int $eventId): void
    {
        $event = $this->owned($eventId);
        $this->authorize('update', $event);

        $timezone = $this->timezone($this->user());
        $starts = $event->beginsAt()->timezone($timezone);
        $ends = $event->finishesAt()->timezone($timezone);

        $this->resetErrorBag();
        $this->editingId = $event->id;
        $this->selectedDate = $starts->toDateString();
        $this->title = $event->title;
        $this->subjectName = $event->subjectLabel();
        $this->location = $event->location ?? '';
        $this->starts = $starts->format('H:i');
        $this->ends = $ends->format('H:i');
        $this->categoryId = $event->category_id;
        $this->composerOpen = true;
    }

    public function closeComposer(): void
    {
        $this->composerOpen = false;
        $this->editingId = null;
    }

    public function save(CreateEvent $create, UpdateEvent $update): void
    {
        $user = $this->user();
        $this->title = trim($this->title);
        $this->subjectName = trim($this->subjectName);
        $this->location = trim($this->location);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:120'],
            'selectedDate' => ['required', 'date_format:Y-m-d'],
            'starts' => ['required', 'date_format:H:i'],
            'ends' => ['required', 'date_format:H:i'],
            'categoryId' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('user_id', $user->id)),
            ],
            'subjectName' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
        ]);

        $attributes = [
            'title' => $validated['title'],
            'date' => $validated['selectedDate'],
            'starts' => $validated['starts'],
            'ends' => $validated['ends'],
            'category_id' => $validated['categoryId'],
            'subject_name' => $validated['subjectName'] ?? null,
            'location' => $validated['location'] ?? null,
        ];

        if ($this->editingId === null) {
            $this->authorize('create', Event::class);
            $create($user, $attributes);
        } else {
            $event = $this->owned($this->editingId);
            $this->authorize('update', $event);
            $update($event, $user, $attributes);
        }

        $this->closeComposer();
    }

    public function remove(DeleteEvent $delete): void
    {
        if ($this->editingId === null) {
            return;
        }

        $event = $this->owned($this->editingId);
        $this->authorize('delete', $event);
        $delete($event);
        $this->closeComposer();
    }

    public function render(): View
    {
        return view('livewire.planning.planning-board', [
            'board' => $this->board(),
            'categories' => $this->user()->categories()->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    /**
     * @return array{
     *     title: string,
     *     label: string,
     *     hint: string,
     *     slots: list<array{time: string, label: string|null}>,
     *     days: list<array{
     *         date: string,
     *         weekday: string,
     *         number: string,
     *         selected: bool,
     *         today: bool,
     *         now: float|null,
     *         blocks: list<array{id: int, title: string, time: string, meta: string, color: string, top: float, height: float}>
     *     }>
     * }
     */
    private function board(): array
    {
        $user = $this->user();
        $timezone = $this->timezone($user);
        Carbon::setLocale(app()->getLocale());

        $selected = $this->selectedMoment($timezone);
        $today = Carbon::now($timezone);
        $shift = ($selected->dayOfWeek - $user->week_starts_on + 7) % 7;
        $weekStart = $selected->copy()->subDays($shift)->startOfDay();
        $place = app(PlaceEventOnDay::class);

        $events = Event::query()
            ->forUser($user)
            ->with(['category', 'subject'])
            ->where('starts_at', '<', $weekStart->copy()->addDays(7)->utc())
            ->where('ends_at', '>', $weekStart->copy()->utc())
            ->orderBy('starts_at')
            ->get();

        $days = [];

        for ($index = 0; $index < 7; $index++) {
            $day = $weekStart->copy()->addDays($index);
            $windowStart = $day->copy()->startOfDay()->addHours(self::START_HOUR);
            $windowEnd = $day->copy()->startOfDay()->addHours(self::END_HOUR);
            $blocks = [];

            foreach ($events as $event) {
                $placed = $place($event->beginsAt(), $event->finishesAt(), $windowStart, $windowEnd);

                if ($placed === null) {
                    continue;
                }

                $starts = $event->beginsAt()->timezone($timezone);
                $ends = $event->finishesAt()->timezone($timezone);
                $meta = $event->subjectLabel();

                if ($meta === '') {
                    $meta = (string) ($event->location ?? '');
                }

                $blocks[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'time' => $this->clock($starts).' – '.$this->clock($ends),
                    'meta' => $meta,
                    'color' => $this->color($event->category?->color),
                    'top' => $placed['top'],
                    'height' => $placed['height'],
                ];
            }

            $now = null;

            if ($today->isSameDay($day) && $today->greaterThan($windowStart) && $today->lessThan($windowEnd)) {
                $now = ($today->getTimestamp() - $windowStart->getTimestamp()) / 60 / 30 * 3;
            }

            $days[] = [
                'date' => $day->toDateString(),
                'weekday' => $day->isoFormat('ddd'),
                'number' => $day->isoFormat('D'),
                'selected' => $day->isSameDay($selected),
                'today' => $day->isSameDay($today),
                'now' => $now,
                'blocks' => $blocks,
            ];
        }

        return [
            'title' => $selected->isSameDay($today) ? (string) __('Today') : $selected->isoFormat('dddd'),
            'label' => $selected->isoFormat('D MMMM'),
            'hint' => (string) __('Tap a time, or add a moment.'),
            'slots' => $this->slots(),
            'days' => $days,
        ];
    }

    /**
     * @return list<array{time: string, label: string|null}>
     */
    private function slots(): array
    {
        $slots = [];

        for ($minutes = self::START_HOUR * 60; $minutes < self::END_HOUR * 60; $minutes += 30) {
            $hour = intdiv($minutes, 60);
            $minute = $minutes % 60;
            $slots[] = [
                'time' => sprintf('%02d:%02d', $hour, $minute),
                'label' => $minute === 0 ? $hour.'h' : null,
            ];
        }

        return $slots;
    }

    private function selectedMoment(string $timezone): Carbon
    {
        $parsed = Carbon::createFromFormat('Y-m-d', $this->selectedDate, $timezone);

        if (! $parsed instanceof Carbon) {
            return Carbon::now($timezone)->startOfDay();
        }

        return $parsed->startOfDay();
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

    private function owned(int $eventId): Event
    {
        $event = Event::query()->forUser($this->user())->with(['category', 'subject'])->find($eventId);

        if (! $event instanceof Event) {
            abort(404);
        }

        return $event;
    }

    private function timezone(User $user): string
    {
        if ($user->timezone !== '') {
            return $user->timezone;
        }

        return (string) config('planner.default_timezone');
    }

    private function user(): User
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }
}
