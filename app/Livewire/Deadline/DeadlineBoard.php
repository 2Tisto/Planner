<?php

declare(strict_types=1);

namespace App\Livewire\Deadline;

use App\Actions\Deadline\SaveDeadline;
use App\Actions\Subject\FindOrCreateSubject;
use App\Enums\DeadlineKind;
use App\Livewire\Concerns\ResolvesUser;
use App\Models\Deadline;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class DeadlineBoard extends Component
{
    use ResolvesUser;

    public string $title = '';

    public string $kind = 'ds';

    public string $date = '';

    public string $time = '08:00';

    public string $location = '';

    public string $coefficient = '';

    public string $subjectName = '';

    /** @var array<string, string> */
    public array $grades = [];

    public function mount(): void
    {
        $this->date = Carbon::now($this->timezone($this->user()))->toDateString();
        $this->loadGrades();
    }

    public function save(SaveDeadline $save, FindOrCreateSubject $subjects): void
    {
        $user = $this->user();
        $this->authorize('create', Deadline::class);

        $rules = [
            'title' => ['required', 'string', 'max:160'],
            'kind' => ['required', 'in:ds,exam,expose,projet,rapport'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:120'],
            'subjectName' => ['nullable', 'string', 'max:120'],
        ];

        if (trim($this->coefficient) !== '') {
            $rules['coefficient'] = ['numeric', 'min:0', 'max:100'];
        }

        $validated = $this->validate($rules);

        $kind = DeadlineKind::tryFrom($validated['kind']) ?? DeadlineKind::Test;
        $name = trim($validated['subjectName'] ?? '');
        $subjectId = $name === '' ? null : $subjects($user, $name, '#bfdbfe')->id;
        $when = Carbon::parse($validated['date'].' '.$validated['time'], $this->timezone($user));

        $save(
            $user,
            $validated['title'],
            $kind,
            $when,
            $this->empty($validated['location'] ?? null),
            $this->empty(isset($validated['coefficient']) ? (string) $validated['coefficient'] : null),
            $subjectId,
        );

        $this->reset(['title', 'location', 'coefficient', 'subjectName']);
        $this->kind = 'ds';
        $this->time = '08:00';
        $this->loadGrades();
    }

    public function saveGrade(int $deadlineId): void
    {
        $deadline = $this->owned($deadlineId);
        $this->authorize('update', $deadline);
        $value = $this->gradeValue($deadlineId);

        if ($value !== '' && ! is_numeric($value)) {
            $this->addError('grades.'.$deadlineId, __('Grade'));

            return;
        }

        $deadline->fill(['grade' => $value === '' ? null : $value]);
        $deadline->save();
        $this->loadGrades();
    }

    public function remove(int $deadlineId): void
    {
        $deadline = $this->owned($deadlineId);
        $this->authorize('delete', $deadline);
        $deadline->delete();
        $this->loadGrades();
    }

    public function render(): View
    {
        $timezone = $this->timezone($this->user());
        $today = Carbon::now($timezone)->startOfDay();
        $rows = [];

        foreach (Deadline::query()->forUser($this->user())->orderBy('happens_at')->get() as $deadline) {
            $when = $deadline->happensAt()->timezone($timezone);
            $days = intdiv($when->copy()->startOfDay()->getTimestamp() - $today->getTimestamp(), 86400);
            $rows[] = [
                'id' => $deadline->id,
                'title' => $deadline->title,
                'kind' => $this->kindLabel($deadline->kind),
                'when' => $when->format('d/m H:i'),
                'countdown' => $this->countdown($days),
                'location' => $deadline->location ?? '',
            ];
        }

        return view('livewire.deadline.deadline-board', ['rows' => $rows]);
    }

    private function loadGrades(): void
    {
        $this->grades = [];

        foreach (Deadline::query()->forUser($this->user())->orderBy('id')->get() as $deadline) {
            $this->grades[(string) $deadline->id] = $deadline->grade !== null ? (string) $deadline->grade : '';
        }
    }

    private function gradeValue(int $deadlineId): string
    {
        foreach ($this->grades as $key => $value) {
            if ((string) $key === (string) $deadlineId) {
                return trim($value);
            }
        }

        return '';
    }

    private function owned(int $deadlineId): Deadline
    {
        $deadline = Deadline::query()->forUser($this->user())->find($deadlineId);

        if (! $deadline instanceof Deadline) {
            abort(404);
        }

        return $deadline;
    }

    private function kindLabel(mixed $kind): string
    {
        $value = $kind instanceof DeadlineKind ? $kind->value : 'ds';

        return match ($value) {
            'exam' => __('Exam'),
            'expose' => __('Talk'),
            'projet' => __('Project'),
            'rapport' => __('Report'),
            default => __('Test'),
        };
    }

    private function countdown(int $days): string
    {
        if ($days < 0) {
            return __('Past');
        }

        if ($days === 0) {
            return __('Today');
        }

        if ($days === 1) {
            return __('Tomorrow');
        }

        return __('In :count days', ['count' => $days]);
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
