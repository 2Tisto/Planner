<?php

declare(strict_types=1);

namespace App\Livewire\Planning;

use App\Actions\Category\SeedDefaultCategories;
use App\Actions\Event\CancelOccurrence;
use App\Actions\Event\ChangeOccurrence;
use App\Actions\Event\ChangeSeries;
use App\Actions\Event\CreateEvent;
use App\Actions\Event\DeleteEvent;
use App\Actions\Event\DuplicateEvent;
use App\Actions\Event\ExpandWeek;
use App\Actions\Event\FindOverlaps;
use App\Actions\Event\MoveEvent;
use App\Actions\Event\Occurrence;
use App\Actions\Event\OccurrenceMoment;
use App\Actions\Event\OverrideOccurrence;
use App\Actions\Event\PlaceEventOnDay;
use App\Actions\Event\ReadPhrase;
use App\Actions\Event\ResizeEvent;
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

    public string $span = 'day';

    public string $phrase = '';

    public bool $composerOpen = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $subjectName = '';

    public string $location = '';

    public string $starts = '08:00';

    public string $ends = '09:00';

    public ?int $categoryId = null;

    public bool $repeats = false;

    public string $until = '';

    public bool $editingSeries = false;

    public string $scope = 'once';

    public string $editingOccurrence = '';

    public string $pendingKind = '';

    public ?int $pendingEventId = null;

    public string $pendingOccurrence = '';

    public string $pendingDate = '';

    public string $pendingStarts = '';

    public string $pendingEnds = '';

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
        $this->clearPending();
    }

    public function showMonth(): void
    {
        $this->span = 'month';
        $this->closeComposer();
        $this->clearPending();
    }

    public function showDay(?string $date = null): void
    {
        if (is_string($date) && $date !== '') {
            $this->selectDate($date);
        }

        $this->span = 'day';
    }

    public function shiftDay(int $days): void
    {
        $moment = Carbon::parse($this->selectedDate);
        $this->selectedDate = ($this->span === 'month' ? $moment->addMonths($days) : $moment->addDays($days))->toDateString();
        $this->closeComposer();
        $this->clearPending();
    }

    public function goToToday(): void
    {
        $this->selectedDate = Carbon::now($this->timezone($this->user()))->toDateString();
        $this->closeComposer();
        $this->clearPending();
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

    public function draft(ReadPhrase $read): void
    {
        $phrase = $read($this->phrase);

        if ($phrase === null) {
            return;
        }

        $date = $phrase->weekday === null
            ? $this->selectedDate
            : $this->upcoming($phrase->weekday);
        $starts = $phrase->starts ?? '08:00';

        $this->openCreate($date, $starts);
        $this->title = $phrase->title;
        $this->subjectName = $phrase->subject;
        $this->location = $phrase->location;

        if ($phrase->ends !== null) {
            $this->ends = $phrase->ends;
        }

        if ($phrase->category !== null) {
            $categoryId = $this->user()->categories()->where('name', $phrase->category)->value('id');
            $this->categoryId = is_numeric($categoryId) ? (int) $categoryId : $this->categoryId;
        }

        if ($phrase->weekly) {
            $this->repeats = true;
            $this->until = Carbon::parse($date)->addWeeks(16)->toDateString();
        }

        $this->phrase = '';
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
        $this->repeats = false;
        $this->until = '';
        $this->editingSeries = false;
        $this->scope = 'once';
        $this->editingOccurrence = '';
        $this->composerOpen = true;
        $this->clearPending();
    }

    public function updatedRepeats(bool $repeats): void
    {
        if (! $repeats || $this->until !== '') {
            return;
        }

        $this->until = Carbon::parse($this->selectedDate)->addWeeks(16)->toDateString();
    }

    public function openEdit(int $eventId, ?string $occurrence = null, ?OccurrenceMoment $moment = null): void
    {
        $event = $this->owned($eventId);
        $this->authorize('update', $event);

        $timezone = $this->timezone($this->user());
        $date = $occurrence ?? $event->beginsAt()->timezone($timezone)->toDateString();
        $starts = $event->beginsAt()->timezone($timezone);
        $ends = $event->finishesAt()->timezone($timezone);
        $title = $event->title;
        $subject = $event->subjectLabel();
        $location = $event->location ?? '';
        $categoryId = $event->category_id;

        if ($event->repeatsWeekly()) {
            $event->loadMissing(['category', 'subject', 'exceptions.category', 'exceptions.subject']);
            $resolved = ($moment ?? app(OccurrenceMoment::class))->__invoke($event, $date);

            if ($resolved === null) {
                abort(404);
            }

            $starts = $resolved['starts']->timezone($timezone);
            $ends = $resolved['ends']->timezone($timezone);
            $title = $resolved['title'];
            $subject = $resolved['subject'];
            $location = $resolved['location'];
            $categoryId = $resolved['categoryId'];
        }

        $this->resetErrorBag();
        $this->editingId = $event->id;
        $this->editingOccurrence = $date;
        $this->editingSeries = $event->repeatsWeekly();
        $this->scope = 'once';
        $this->repeats = $event->repeatsWeekly();
        $this->until = $event->untilDate() ?? '';
        $this->selectedDate = $starts->toDateString();
        $this->title = $title;
        $this->subjectName = $subject;
        $this->location = $location;
        $this->starts = $starts->format('H:i');
        $this->ends = $ends->format('H:i');
        $this->categoryId = $categoryId;
        $this->composerOpen = true;
        $this->clearPending();
    }

    public function closeComposer(): void
    {
        $this->composerOpen = false;
        $this->editingId = null;
    }

    public function save(CreateEvent $create, UpdateEvent $update, OverrideOccurrence $override): void
    {
        $user = $this->user();
        $this->title = trim($this->title);
        $this->subjectName = trim($this->subjectName);
        $this->location = trim($this->location);
        $existing = null;

        if ($this->editingId === null) {
            $this->authorize('create', Event::class);
        } else {
            $existing = $this->owned($this->editingId);
            $this->authorize('update', $existing);

            if ($existing->repeatsWeekly()) {
                $this->repeats = $this->scope === 'series';
            }
        }

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
            'until' => [
                Rule::excludeIf(fn (): bool => ! $this->repeats),
                'required',
                'date_format:Y-m-d',
                'after_or_equal:'.$this->firstDate($existing),
            ],
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
        $until = $this->repeats ? $validated['until'] : null;

        if ($existing instanceof Event && $existing->repeatsWeekly() && $this->scope !== 'series') {
            $override($existing, $user, $this->editingOccurrence, $attributes);
        } elseif ($existing instanceof Event) {
            $update($existing, $user, $attributes, $this->repeats, $until);
        } else {
            $create($user, $attributes, $this->repeats, $until);
        }

        $this->closeComposer();
    }

    public function moveBlock(int $eventId, string $occurrence, string $date, string $starts, MoveEvent $move): void
    {
        $event = $this->owned($eventId);
        $this->authorize('update', $event);

        $validated = validator([
            'occurrence' => $occurrence,
            'date' => $date,
            'starts' => $starts,
        ], [
            'occurrence' => ['required', 'date_format:Y-m-d'],
            'date' => ['required', 'date_format:Y-m-d'],
            'starts' => ['required', 'date_format:H:i'],
        ])->validate();

        if ($event->repeatsWeekly()) {
            $this->pendingKind = 'move';
            $this->pendingEventId = $event->id;
            $this->pendingOccurrence = $validated['occurrence'];
            $this->pendingDate = $validated['date'];
            $this->pendingStarts = $validated['starts'];
            $this->pendingEnds = '';

            return;
        }

        $move($event, $validated['date'], $validated['starts']);
    }

    public function resizeBlock(int $eventId, string $occurrence, string $ends, ResizeEvent $resize): void
    {
        $event = $this->owned($eventId);
        $this->authorize('update', $event);

        $validated = validator([
            'occurrence' => $occurrence,
            'ends' => $ends,
        ], [
            'occurrence' => ['required', 'date_format:Y-m-d'],
            'ends' => ['required', 'date_format:H:i'],
        ])->validate();

        if ($event->repeatsWeekly()) {
            $this->pendingKind = 'resize';
            $this->pendingEventId = $event->id;
            $this->pendingOccurrence = $validated['occurrence'];
            $this->pendingEnds = $validated['ends'];

            return;
        }

        $resize($event, $validated['ends']);
    }

    public function applyPending(string $scope, ChangeOccurrence $once, ChangeSeries $series): void
    {
        if ($this->pendingEventId === null || ($scope !== 'once' && $scope !== 'series')) {
            return;
        }

        $event = $this->owned($this->pendingEventId);
        $this->authorize('update', $event);

        if ($this->pendingKind === 'move') {
            if ($scope === 'once') {
                $once->move($event, $this->pendingOccurrence, $this->pendingDate, $this->pendingStarts);
            } else {
                $series->move($event, $this->pendingOccurrence, $this->pendingDate, $this->pendingStarts);
            }
        }

        if ($this->pendingKind === 'resize') {
            if ($scope === 'once') {
                $once->resize($event, $this->pendingOccurrence, $this->pendingEnds);
            } else {
                $series->resize($event, $this->pendingOccurrence, $this->pendingEnds);
            }
        }

        $this->clearPending();
    }

    public function cancelPending(): void
    {
        $this->clearPending();
    }

    public function duplicate(DuplicateEvent $duplicate, ?int $eventId = null): void
    {
        $id = $eventId ?? $this->editingId;

        if ($id === null) {
            return;
        }

        $event = $this->owned($id);
        $this->authorize('create', Event::class);
        $timezone = $this->timezone($this->user());
        $start = Carbon::parse($this->selectedDate.' '.$this->starts, $timezone);
        $end = Carbon::parse($this->selectedDate.' '.$this->ends, $timezone);

        if ($end->lessThanOrEqualTo($start)) {
            $end = $end->addDay();
        }

        $copy = $duplicate($event, $start, $end);
        $this->selectedDate = $copy->beginsAt()->timezone($timezone)->toDateString();
        $this->closeComposer();
    }

    public function remove(DeleteEvent $delete, CancelOccurrence $cancel): void
    {
        if ($this->editingId === null) {
            return;
        }

        $event = $this->owned($this->editingId);
        $this->authorize('delete', $event);

        if ($event->repeatsWeekly() && $this->scope !== 'series') {
            $cancel($event, $this->editingOccurrence);
        } else {
            $delete($event);
        }

        $this->closeComposer();
    }

    public function render(): View
    {
        return view('livewire.planning.planning-board', [
            'board' => $this->span === 'month' ? $this->month() : $this->board(),
            'focus' => $this->focus(),
            'categories' => $this->user()->categories()->orderBy('position')->orderBy('id')->get(),
            'overlaps' => $this->composerOpen ? $this->overlaps() : [],
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
     *         blocks: list<array{id: int, date: string, recurring: bool, title: string, time: string, meta: string, color: string, top: float, height: float, duration: int}>
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

        $occurrences = app(ExpandWeek::class)($user, $weekStart, $weekStart->copy()->addDays(7));

        $days = [];

        for ($index = 0; $index < 7; $index++) {
            $day = $weekStart->copy()->addDays($index);
            $windowStart = $day->copy()->startOfDay()->addHours(self::START_HOUR);
            $windowEnd = $day->copy()->startOfDay()->addHours(self::END_HOUR);
            $blocks = [];

            foreach ($occurrences as $occurrence) {
                $placed = $place($occurrence->starts, $occurrence->ends, $windowStart, $windowEnd);

                if ($placed === null) {
                    continue;
                }

                $starts = $occurrence->starts->timezone($timezone);
                $ends = $occurrence->ends->timezone($timezone);

                $blocks[] = [
                    'id' => $occurrence->id,
                    'date' => $occurrence->date,
                    'recurring' => $occurrence->recurring,
                    'title' => $occurrence->title,
                    'time' => $this->clock($starts).' – '.$this->clock($ends),
                    'meta' => $occurrence->meta,
                    'color' => $this->color($occurrence->color),
                    'top' => $placed['top'],
                    'height' => $placed['height'],
                    'duration' => (int) round(($occurrence->ends->getTimestamp() - $occurrence->starts->getTimestamp()) / 60),
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
            'hint' => (string) __('Tap a time to add. Drag a block to move it.'),
            'slots' => $this->slots(),
            'days' => $days,
        ];
    }

    /**
     * @return array{
     *     current: array{id: int, date: string, title: string, time: string, meta: string, color: string}|null,
     *     next: array{id: int, date: string, title: string, time: string, meta: string, color: string}|null,
     *     quiet: 'open'|'waiting'|'clear'|null
     * }
     */
    private function focus(): array
    {
        $user = $this->user();
        $timezone = $this->timezone($user);
        $now = Carbon::now($timezone);
        $day = $now->copy()->startOfDay();
        $occurrences = app(ExpandWeek::class)($user, $day, $day->copy()->addDay());
        $ongoing = [];
        $upcoming = [];
        $finished = false;

        foreach ($occurrences as $occurrence) {
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
            'current' => $current instanceof Occurrence ? $this->presentFocus($current, $timezone) : null,
            'next' => $next instanceof Occurrence ? $this->presentFocus($next, $timezone) : null,
            'quiet' => $quiet,
        ];
    }

    /**
     * @return array{id: int, date: string, title: string, time: string, meta: string, color: string}
     */
    private function presentFocus(Occurrence $occurrence, string $timezone): array
    {
        $starts = $occurrence->starts->copy()->timezone($timezone);
        $ends = $occurrence->ends->copy()->timezone($timezone);

        return [
            'id' => $occurrence->id,
            'date' => $occurrence->date,
            'title' => $occurrence->title,
            'time' => $this->clock($starts).' – '.$this->clock($ends),
            'meta' => $occurrence->meta,
            'color' => $this->color($occurrence->color),
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

    /**
     * @return array{
     *     title: string,
     *     label: string,
     *     hint: string,
     *     weekdays: list<string>,
     *     cells: list<array{
     *         date: string,
     *         number: string,
     *         inMonth: bool,
     *         today: bool,
     *         selected: bool,
     *         blocks: list<array{id: int, date: string, title: string, color: string}>,
     *         more: int
     *     }>
     * }
     */
    private function month(): array
    {
        $user = $this->user();
        $timezone = $this->timezone($user);
        Carbon::setLocale(app()->getLocale());

        $selected = $this->selectedMoment($timezone);
        $today = Carbon::now($timezone);
        $monthStart = $selected->copy()->startOfMonth();
        $monthEnd = $selected->copy()->endOfMonth()->startOfDay();
        $lead = ($monthStart->dayOfWeek - $user->week_starts_on + 7) % 7;
        $gridStart = $monthStart->copy()->subDays($lead)->startOfDay();
        $lastColumn = ($user->week_starts_on + 6) % 7;
        $tail = ($lastColumn - $monthEnd->dayOfWeek + 7) % 7;
        $gridEnd = $monthEnd->copy()->addDays($tail + 1)->startOfDay();
        $occurrences = app(ExpandWeek::class)($user, $gridStart, $gridEnd);
        $byDate = [];

        foreach ($occurrences as $occurrence) {
            $cursor = $occurrence->starts->copy()->timezone($timezone)->startOfDay();
            $finish = $occurrence->ends->copy()->timezone($timezone);

            if ($finish->isStartOfDay()) {
                $finish->subSecond();
            }

            $last = $finish->copy()->startOfDay();

            while ($cursor->lessThanOrEqualTo($last)) {
                $byDate[$cursor->toDateString()][] = [
                    'id' => $occurrence->id,
                    'date' => $occurrence->date,
                    'title' => $occurrence->title,
                    'color' => $this->color($occurrence->color),
                ];
                $cursor->addDay();
            }
        }

        $weekdays = [];

        for ($index = 0; $index < 7; $index++) {
            $weekdays[] = $gridStart->copy()->addDays($index)->isoFormat('ddd');
        }

        $cells = [];

        for ($day = $gridStart->copy(); $day->lessThan($gridEnd); $day->addDay()) {
            $blocks = $byDate[$day->toDateString()] ?? [];
            $cells[] = [
                'date' => $day->toDateString(),
                'number' => $day->isoFormat('D'),
                'inMonth' => $day->month === $monthStart->month,
                'today' => $day->isSameDay($today),
                'selected' => $day->isSameDay($selected),
                'blocks' => array_slice($blocks, 0, 3),
                'more' => max(0, count($blocks) - 3),
            ];
        }

        return [
            'title' => $monthStart->isoFormat('MMMM'),
            'label' => $monthStart->isoFormat('YYYY'),
            'hint' => (string) __('Tap a day to open it.'),
            'weekdays' => $weekdays,
            'cells' => $cells,
        ];
    }

    private function upcoming(int $weekday): string
    {
        $moment = $this->selectedMoment($this->timezone($this->user()));
        $shift = ($weekday - $moment->dayOfWeek + 7) % 7;

        return $moment->addDays($shift)->toDateString();
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

    /**
     * @return list<string>
     */
    private function overlaps(): array
    {
        $user = $this->user();
        $timezone = $this->timezone($user);

        try {
            $start = Carbon::parse($this->selectedDate.' '.$this->starts, $timezone);
            $end = Carbon::parse($this->selectedDate.' '.$this->ends, $timezone);
        } catch (\Throwable) {
            return [];
        }

        if ($end->lessThanOrEqualTo($start)) {
            $end = $end->addDay();
        }

        return app(FindOverlaps::class)(
            $user,
            $start,
            $end,
            $this->editingId,
            $this->editingOccurrence !== '' ? $this->editingOccurrence : null,
        );
    }

    private function firstDate(?Event $event): string
    {
        if ($event instanceof Event && $event->repeatsWeekly()) {
            $timezone = $event->timezone !== '' ? $event->timezone : (string) config('planner.default_timezone');

            return $event->beginsAt()->timezone($timezone)->toDateString();
        }

        return $this->selectedDate;
    }

    private function clearPending(): void
    {
        $this->pendingKind = '';
        $this->pendingEventId = null;
        $this->pendingOccurrence = '';
        $this->pendingDate = '';
        $this->pendingStarts = '';
        $this->pendingEnds = '';
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
