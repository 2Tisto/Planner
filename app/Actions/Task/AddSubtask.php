<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

final class AddSubtask
{
    public function __invoke(User $user, Task $task, string $title): Subtask
    {
        return Subtask::query()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'title' => $title,
            'done' => false,
        ]);
    }
}
