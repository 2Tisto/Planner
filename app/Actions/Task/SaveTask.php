<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;

final class SaveTask
{
    public function __invoke(
        User $user,
        string $title,
        TaskPriority $priority,
        ?string $dueOn,
        ?int $estimate,
        ?int $subjectId,
    ): Task {
        return Task::query()->create([
            'user_id' => $user->id,
            'subject_id' => $subjectId,
            'title' => $title,
            'priority' => $priority,
            'status' => TaskStatus::Todo,
            'due_on' => $dueOn,
            'estimate_minutes' => $estimate,
        ]);
    }
}
