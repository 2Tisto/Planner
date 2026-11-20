<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Habit;
use App\Models\User;

class HabitPolicy
{
    use ChecksOwner;

    public function update(User $user, Habit $habit): bool
    {
        return $this->owns($user, $habit);
    }

    public function delete(User $user, Habit $habit): bool
    {
        return $this->owns($user, $habit);
    }
}
