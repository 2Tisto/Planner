<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\User;

trait ResolvesUser
{
    protected function user(): User
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }

    protected function timezone(User $user): string
    {
        if ($user->timezone !== '') {
            return $user->timezone;
        }

        $fallback = config('planner.default_timezone');

        return is_string($fallback) && $fallback !== '' ? $fallback : 'Africa/Porto-Novo';
    }
}
