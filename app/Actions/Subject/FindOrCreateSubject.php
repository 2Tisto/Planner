<?php

declare(strict_types=1);

namespace App\Actions\Subject;

use App\Models\Subject;
use App\Models\User;

final class FindOrCreateSubject
{
    public function __invoke(User $user, string $name, string $color): Subject
    {
        $existing = Subject::query()->forUser($user)->where('name', $name)->first();

        if ($existing instanceof Subject) {
            return $existing;
        }

        return Subject::query()->create([
            'user_id' => $user->id,
            'name' => $name,
            'code' => null,
            'teacher' => null,
            'default_room' => null,
            'coefficient' => null,
            'color' => strtolower($color),
        ]);
    }
}
