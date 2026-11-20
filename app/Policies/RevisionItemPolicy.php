<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RevisionItem;
use App\Models\User;

class RevisionItemPolicy
{
    use ChecksOwner;

    public function update(User $user, RevisionItem $item): bool
    {
        return $this->owns($user, $item);
    }

    public function delete(User $user, RevisionItem $item): bool
    {
        return $this->owns($user, $item);
    }
}
