<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Event\ExpandWeek;
use App\Models\Share;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class ShareController extends Controller
{
    public function __invoke(string $token, ExpandWeek $expand): View
    {
        $share = Share::query()->where('token', $token)->first();

        if (! $share instanceof Share) {
            abort(404);
        }

        $user = $share->user;

        if (! $user instanceof User) {
            abort(404);
        }

        $timezone = $user->timezone !== '' ? $user->timezone : 'Africa/Porto-Novo';
        $start = Carbon::now($timezone)->startOfWeek(Carbon::MONDAY);
        $occurrences = $expand($user, $start, $start->copy()->addWeek());
        $days = [];

        foreach ($occurrences as $occurrence) {
            $moment = $occurrence->starts->copy()->timezone($timezone);
            $key = $moment->toDateString();
            $days[$key][] = [
                'title' => $occurrence->title,
                'time' => $moment->format('H:i'),
            ];
        }

        ksort($days);

        return view('share', [
            'owner' => $user->name,
            'days' => $days,
        ]);
    }
}
