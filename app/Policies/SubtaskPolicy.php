<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Subtask;
use App\Models\User;

class SubtaskPolicy
{
    use ChecksOwner;

    public function update(User $user, Subtask $subtask): bool
    {
        return $this->owns($user, $subtask);
    }

    public function delete(User $user, Subtask $subtask): bool
    {
        return $this->owns($user, $subtask);
    }
}
