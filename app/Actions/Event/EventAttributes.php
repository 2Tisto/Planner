<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Actions\Subject\FindOrCreateSubject;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Carbon;

final class EventAttributes
{
    public function __construct(private FindOrCreateSubject $subjects) {}

    /**
     * @param  array{
     *     title: string,
     *     date: string,
     *     starts: string,
     *     ends: string,
     *     category_id: int|null,
     *     subject_name: string|null,
     *     location: string|null
     * }  $attributes
     * @return array{
     *     title: string,
     *     location: string|null,
     *     starts_at: Carbon,
     *     ends_at: Carbon,
     *     timezone: string,
     *     category_id: int|null,
     *     subject_id: int|null
     * }
     */
    public function __invoke(User $user, array $attributes): array
    {
        $timezone = $user->timezone !== '' ? $user->timezone : (string) config('planner.default_timezone');
        $starts = Carbon::parse($attributes['date'].' '.$attributes['starts'], $timezone);
        $ends = Carbon::parse($attributes['date'].' '.$attributes['ends'], $timezone);

        if ($ends->lessThanOrEqualTo($starts)) {
            $ends = $ends->addDay();
        }

        $category = null;

        if ($attributes['category_id'] !== null) {
            $category = Category::query()->forUser($user)->find($attributes['category_id']);

            if (! $category instanceof Category) {
                abort(404);
            }
        }

        $subjectName = trim((string) $attributes['subject_name']);
        $subjectId = null;

        if ($subjectName !== '') {
            $color = $category->color ?? '#e5e7eb';
            $subjectId = $this->subjects->__invoke($user, $subjectName, $color)->id;
        }

        $location = trim((string) $attributes['location']);

        return [
            'title' => trim($attributes['title']),
            'location' => $location !== '' ? $location : null,
            'starts_at' => $starts->utc(),
            'ends_at' => $ends->utc(),
            'timezone' => $timezone,
            'category_id' => $category?->id,
            'subject_id' => $subjectId,
        ];
    }
}
