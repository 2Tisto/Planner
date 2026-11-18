<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default timezone
    |--------------------------------------------------------------------------
    |
    | Instants are stored in UTC. This timezone is the default display zone
    | for a new account. Africa/Porto-Novo does not observe daylight saving.
    |
    */

    'default_timezone' => env('PLANNER_DEFAULT_TIMEZONE', 'Africa/Porto-Novo'),

    'default_locale' => env('PLANNER_DEFAULT_LOCALE', 'fr'),

    /*
    |--------------------------------------------------------------------------
    | Week grid
    |--------------------------------------------------------------------------
    |
    | The grid draws a line every slot. Dragging and resizing snap to the
    | smaller step so a 1h30 course still lands cleanly.
    |
    */

    'slot_minutes' => (int) env('PLANNER_SLOT_MINUTES', 30),

    'snap_minutes' => (int) env('PLANNER_SNAP_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | Default categories
    |--------------------------------------------------------------------------
    |
    | Created once per account at registration. Colors are light backgrounds
    | meant for dark text.
    |
    */

    'default_categories' => [
        ['name' => 'Cours', 'color' => '#BFDBFE'],
        ['name' => 'TD/TP', 'color' => '#A7F3D0'],
        ['name' => 'Révisions', 'color' => '#DDD6FE'],
        ['name' => 'Projet', 'color' => '#FDE68A'],
        ['name' => 'Cybersécurité/CTF', 'color' => '#FECACA'],
        ['name' => 'Exposé', 'color' => '#FBCFE8'],
        ['name' => 'Sport', 'color' => '#BBF7D0'],
        ['name' => 'Repas', 'color' => '#FED7AA'],
        ['name' => 'Transport', 'color' => '#E5E7EB'],
        ['name' => 'Repos', 'color' => '#CFFAFE'],
        ['name' => 'Perso', 'color' => '#F5D0FE'],
        ['name' => 'Sommeil', 'color' => '#C7D2FE'],
    ],

];
