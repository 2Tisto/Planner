<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    use ChecksOwner;

    public function update(User $user, Subject $subject): bool
    {
        return $this->owns($user, $subject);
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $this->owns($user, $subject);
    }
}
