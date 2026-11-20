<?php

declare(strict_types=1);

namespace App\Actions\Revision;

use App\Enums\RevisionStage;
use App\Models\RevisionItem;
use App\Models\Subject;
use App\Models\User;

final class SaveRevision
{
    public function __invoke(User $user, Subject $subject, string $title): RevisionItem
    {
        return RevisionItem::query()->create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'title' => $title,
            'stage' => RevisionStage::Reading,
            'confidence' => 1,
        ]);
    }
}
