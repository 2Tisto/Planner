<?php

declare(strict_types=1);

namespace App\Actions\Deadline;

use App\Enums\DeadlineKind;
use App\Models\Deadline;
use App\Models\User;
use Illuminate\Support\Carbon;

final class SaveDeadline
{
    public function __invoke(
        User $user,
        string $title,
        DeadlineKind $kind,
        Carbon $happensAt,
        ?string $location,
        ?string $coefficient,
        ?int $subjectId,
    ): Deadline {
        return Deadline::query()->create([
            'user_id' => $user->id,
            'subject_id' => $subjectId,
            'title' => $title,
            'kind' => $kind,
            'happens_at' => $happensAt->utc(),
            'location' => $location,
            'coefficient' => $coefficient,
            'grade' => null,
        ]);
    }
}
