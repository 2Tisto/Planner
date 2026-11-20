<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Planner\BuildCalendar;
use App\Models\User;
use Illuminate\Http\Response;

class CalendarController extends Controller
{
    public function __invoke(BuildCalendar $calendar): Response
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        return response($calendar($user), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="semaine.ics"',
        ]);
    }
}
