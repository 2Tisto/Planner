<?php

declare(strict_types=1);

namespace App\Livewire\Task;

use App\Actions\Study\RecordStudy;
use App\Actions\Subject\FindOrCreateSubject;
use App\Actions\Task\AddSubtask;
use App\Actions\Task\SaveTask;
use App\Enums\TaskPriority;
use App\Livewire\Concerns\ResolvesUser;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class TaskBoard extends Component
{
    use ResolvesUser;

    public string $title = '';

    public string $priority = 'B';

    public string $due = '';

    public string $estimate = '';

    public string $subjectName = '';

    public string $step = '';

    public ?int $openId = null;

    public function save(SaveTask $save, FindOrCreateSubject $subjects): void
    {
        $user = $this->user();
        $this->authorize('create', Task::class);

        $rules = [
            'title' => ['required', 'string', 'max:160'],
            'priority' => ['required', 'in:A,B,C'],
            'due' => ['nullable', 'date'],
            'subjectName' => ['nullable', 'string', 'max:120'],
        ];

        if (trim($this->estimate) !== '') {
            $rules['estimate'] = ['integer', 'min:5', 'max:600'];
        }

        $validated = $this->validate($rules);

        $subjectId = null;
        $name = trim($validated['subjectName'] ?? '');

        if ($name !== '') {
            $subjectId = $subjects($user, $name, '#bfdbfe')->id;
        }

        $priority = TaskPriority::tryFrom($validated['priority']) ?? TaskPriority::B;
        $estimate = isset($validated['estimate']) && $validated['estimate'] !== ''
            ? (int) $validated['estimate']
            : null;

        $save(
            $user,
            $validated['title'],
            $priority,
            $this->empty($validated['due'] ?? null),
            $estimate,
            $subjectId,
        );

        $this->reset(['title', 'due', 'estimate', 'subjectName']);
        $this->priority = 'B';
    }

    public function cycle(int $taskId): void
    {
        $task = $this->owned($taskId);
        $this->authorize('update', $task);
        $task->fill(['status' => $task->status()->next()]);
        $task->save();
    }

    public function open(int $taskId): void
    {
        $task = $this->owned($taskId);
        $this->authorize('view', $task);
        $this->openId = $this->openId === $task->id ? null : $task->id;
        $this->step = '';
    }

    public function addStep(int $taskId, AddSubtask $add): void
    {
        $task = $this->owned($taskId);
        $this->authorize('update', $task);
        $validated = $this->validate(['step' => ['required', 'string', 'max:160']]);
        $add($this->user(), $task, $validated['step']);
        $this->step = '';
        $this->openId = $task->id;
    }

    public function toggleStep(int $stepId): void
    {
        $step = Subtask::query()->forUser($this->user())->find($stepId);

        if (! $step instanceof Subtask) {
            abort(404);
        }

        $this->authorize('update', $step);
        $step->done = ! $step->done;
        $step->save();
    }

    public function note(int $taskId, RecordStudy $record): void
    {
        $task = $this->owned($taskId);
        $this->authorize('update', $task);
        $record($this->user(), $task, 25);
    }

    public function remove(int $taskId): void
    {
        $task = $this->owned($taskId);
        $this->authorize('delete', $task);
        $task->delete();
    }

    public function render(): View
    {
        return view('livewire.task.task-board', [
            'tasks' => Task::query()->forUser($this->user())->with('subtasks')->orderBy('id')->get(),
        ]);
    }

    private function owned(int $taskId): Task
    {
        $task = Task::query()->forUser($this->user())->find($taskId);

        if (! $task instanceof Task) {
            abort(404);
        }

        return $task;
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
