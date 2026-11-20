<?php

declare(strict_types=1);

namespace App\Actions\Share;

use App\Models\Share;
use App\Models\User;
use Illuminate\Support\Str;

final class OpenShare
{
    public function __invoke(User $user): Share
    {
        $existing = Share::query()->where('user_id', $user->id)->first();

        if ($existing instanceof Share) {
            return $existing;
        }

        return Share::query()->create([
            'user_id' => $user->id,
            'token' => Str::random(40),
        ]);
    }
}
