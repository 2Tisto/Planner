<?php

declare(strict_types=1);

namespace App\Actions\Subject;

use App\Models\Subject;
use App\Models\User;

final class SaveSubject
{
    public function __invoke(
        User $user,
        ?Subject $subject,
        string $name,
        ?string $code,
        ?string $teacher,
        ?string $room,
        ?string $coefficient,
        string $color,
    ): Subject {
        $payload = [
            'name' => $name,
            'code' => $code,
            'teacher' => $teacher,
            'default_room' => $room,
            'coefficient' => $coefficient,
            'color' => strtolower($color),
        ];

        if ($subject instanceof Subject) {
            $subject->fill($payload);
            $subject->save();

            return $subject;
        }

        return Subject::query()->create(['user_id' => $user->id, ...$payload]);
    }
}
