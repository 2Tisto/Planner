<?php

declare(strict_types=1);

namespace App\Actions\Category;

use App\Models\Category;
use App\Models\User;

final class CreateCategory
{
    public function __invoke(User $user, string $name, string $color): Category
    {
        $position = Category::query()->forUser($user)->max('position');

        return Category::query()->create([
            'user_id' => $user->id,
            'name' => $name,
            'color' => strtolower($color),
            'icon' => null,
            'position' => $position === null ? 0 : ((int) $position) + 1,
            'is_system' => false,
        ]);
    }
}
