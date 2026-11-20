<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait ChecksOwner
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    protected function owns(User $user, Model $model): bool
    {
        $owner = $model->getAttribute('user_id');

        return is_numeric($owner) && (int) $owner === $user->id;
    }
}
