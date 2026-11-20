<?php

declare(strict_types=1);

namespace App\Livewire\Review;

use App\Actions\Event\ExpandWeek;
use App\Actions\Review\SaveReview;
use App\Livewire\Concerns\ResolvesUser;
use App\Models\Category;
use App\Models\Event;
use App\Models\StudySession;
use App\Models\WeeklyReview;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ReviewBoard extends Component
{
    use ResolvesUser;

    public string $wins = '';

    public string $hard = '';

    public string $nextStep = '';

    public string $satisfaction = '';

    public function mount(): void
    {
        $review = $this->current();

        if (! $review instanceof WeeklyReview) {
            return;
        }

        $this->wins = $review->wins ?? '';
        $this->hard = $review->hard ?? '';
        $this->nextStep = $review->next_step ?? '';
        $this->satisfaction = $review->satisfaction !== null ? (string) $review->satisfaction : '';
    }

    public function save(SaveReview $save): void
    {
        $rules = [
            'wins' => ['nullable', 'string', 'max:2000'],
            'hard' => ['nullable', 'string', 'max:2000'],
            'nextStep' => ['nullable', 'string', 'max:2000'],
        ];

        if (trim($this->satisfaction) !== '') {
            $rules['satisfaction'] = ['integer', 'min:1', 'max:5'];
        }

        $validated = $this->validate($rules);

        $save(
            $this->user(),
            $this->weekStart()->toDateString(),
            $this->empty($validated['wins'] ?? null),
            $this->empty($validated['hard'] ?? null),
            $this->empty($validated['nextStep'] ?? null),
            isset($validated['satisfaction']) && $validated['satisfaction'] !== ''
                ? (int) $validated['satisfaction']
                : null,
        );
    }

    public function render(ExpandWeek $expand): View
    {
        $user = $this->user();
        $start = $this->weekStart();
        $end = $start->copy()->addWeek();
        $labels = [];

        foreach (Event::query()->forUser($user)->with('category')->get() as $event) {
            $category = $event->category;
            $labels[$event->id] = $category instanceof Category ? $category->name : __('Other');
        }

        $totals = [];

        foreach ($expand($user, $start, $end) as $occurrence) {
            $label = $labels[$occurrence->id] ?? __('Other');
            $minutes = max(0, (int) round(($occurrence->ends->getTimestamp() - $occurrence->starts->getTimestamp()) / 60));
            $totals[$label] = ($totals[$label] ?? 0) + $minutes;
        }

        $actual = (int) StudySession::query()
            ->forUser($user)
            ->where('started_at', '>=', $start->copy()->utc())
            ->where('started_at', '<', $end->copy()->utc())
            ->sum('minutes');

        return view('livewire.review.review-board', [
            'totals' => $totals,
            'actual' => $actual,
        ]);
    }

    private function current(): ?WeeklyReview
    {
        $review = WeeklyReview::query()
            ->forUser($this->user())
            ->whereDate('week_start', $this->weekStart()->toDateString())
            ->first();

        return $review instanceof WeeklyReview ? $review : null;
    }

    private function weekStart(): Carbon
    {
        return Carbon::now($this->timezone($this->user()))->startOfWeek(Carbon::MONDAY);
    }

    private function empty(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
