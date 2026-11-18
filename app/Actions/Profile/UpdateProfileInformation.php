<?php

declare(strict_types=1);

namespace App\Actions\Profile;

use App\Models\User;
use Illuminate\Support\Facades\App;

final class UpdateProfileInformation
{
    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     filiere: string|null,
     *     niveau: string|null,
     *     semestre: string|null,
     *     timezone: string,
     *     locale: string,
     *     week_starts_on: int
     * }  $attributes
     */
    public function __invoke(User $user, array $attributes): void
    {
        foreach (['filiere', 'niveau', 'semestre'] as $field) {
            if (($attributes[$field] ?? null) === '') {
                $attributes[$field] = null;
            }
        }

        $attributes['week_starts_on'] = (int) $attributes['week_starts_on'];

        $user->fill($attributes);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        App::setLocale($user->locale);
    }
}
