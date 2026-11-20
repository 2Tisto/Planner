<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ShareController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('partage/{token}', ShareController::class)
    ->where('token', '[A-Za-z0-9]+')
    ->name('share.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'home')->name('dashboard');
    Route::view('planning', 'planning')->name('planning.board');
    Route::view('taches', 'tasks')->name('tasks.index');
    Route::view('echeances', 'deadlines')->name('deadlines.index');
    Route::view('matieres', 'subjects')->name('subjects.index');
    Route::view('habitudes', 'habits')->name('habits.index');
    Route::view('revisions', 'revisions')->name('revisions.index');
    Route::view('bilan', 'review')->name('reviews.index');
    Route::view('categories', 'categories')->name('categories.index');
    Route::get('planning.ics', CalendarController::class)->name('planning.calendar');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
