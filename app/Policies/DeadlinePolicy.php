<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Deadline;
use App\Models\User;

class DeadlinePolicy
{
    use ChecksOwner;

    public function update(User $user, Deadline $deadline): bool
    {
        return $this->owns($user, $deadline);
    }

    public function delete(User $user, Deadline $deadline): bool
    {
        return $this->owns($user, $deadline);
    }
}
