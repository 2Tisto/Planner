<?php

declare(strict_types=1);

namespace App\Actions\Study;

use App\Models\StudySession;
use App\Models\Subject;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;

final class RecordStudy
{
    public function __invoke(User $user, ?Task $task, int $minutes, ?Subject $subject = null): StudySession
    {
        $ended = Carbon::now()->utc();

        $subjectId = null;

        if ($subject instanceof Subject) {
            $subjectId = $subject->id;
        } elseif ($task instanceof Task) {
            $subjectId = $task->subject_id;
        }

        return StudySession::query()->create([
            'user_id' => $user->id,
            'task_id' => $task instanceof Task ? $task->id : null,
            'subject_id' => $subjectId,
            'started_at' => $ended->copy()->subMinutes($minutes),
            'ended_at' => $ended,
            'minutes' => $minutes,
        ]);
    }
}
