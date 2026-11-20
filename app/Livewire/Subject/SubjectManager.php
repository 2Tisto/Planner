<?php

declare(strict_types=1);

namespace App\Livewire\Subject;

use App\Actions\Subject\SaveSubject;
use App\Livewire\Concerns\ResolvesUser;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SubjectManager extends Component
{
    use ResolvesUser;

    public ?int $editingId = null;

    public string $name = '';

    public string $code = '';

    public string $teacher = '';

    public string $room = '';

    public string $coefficient = '';

    public string $color = '#BFDBFE';

    public function edit(int $subjectId): void
    {
        $subject = $this->owned($subjectId);
        $this->authorize('update', $subject);
        $this->editingId = $subject->id;
        $this->name = $subject->name;
        $this->code = $subject->code ?? '';
        $this->teacher = $subject->teacher ?? '';
        $this->room = $subject->default_room ?? '';
        $this->coefficient = $subject->coefficient !== null ? (string) $subject->coefficient : '';
        $this->color = $subject->color;
    }

    public function cancel(): void
    {
        $this->reset(['editingId', 'name', 'code', 'teacher', 'room', 'coefficient']);
        $this->color = '#BFDBFE';
    }

    public function save(SaveSubject $save): void
    {
        $user = $this->user();
        $subject = $this->editingId === null ? null : $this->owned($this->editingId);
        $this->authorize($subject instanceof Subject ? 'update' : 'create', $subject ?? Subject::class);

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('subjects', 'name')->where(fn ($query) => $query->where('user_id', $user->id))->ignore($subject?->id),
            ],
            'code' => ['nullable', 'string', 'max:40'],
            'teacher' => ['nullable', 'string', 'max:120'],
            'room' => ['nullable', 'string', 'max:120'],
            'coefficient' => trim($this->coefficient) === '' ? ['nullable'] : ['numeric', 'min:0', 'max:100'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $save(
            $user,
            $subject,
            $validated['name'],
            $this->empty($validated['code'] ?? null),
            $this->empty($validated['teacher'] ?? null),
            $this->empty($validated['room'] ?? null),
            $this->empty(isset($validated['coefficient']) ? (string) $validated['coefficient'] : null),
            $validated['color'],
        );

        $this->cancel();
    }

    public function remove(int $subjectId): void
    {
        $subject = $this->owned($subjectId);
        $this->authorize('delete', $subject);
        $subject->delete();

        if ($this->editingId === $subjectId) {
            $this->cancel();
        }
    }

    public function render(): View
    {
        return view('livewire.subject.subject-manager', [
            'subjects' => Subject::query()->forUser($this->user())->orderBy('name')->get(),
        ]);
    }

    private function owned(int $subjectId): Subject
    {
        $subject = Subject::query()->forUser($this->user())->find($subjectId);

        if (! $subject instanceof Subject) {
            abort(404);
        }

        return $subject;
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
