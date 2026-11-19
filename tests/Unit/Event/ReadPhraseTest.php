<?php

declare(strict_types=1);

use App\Actions\Event\ReadPhrase;

test('a course phrase keeps the day, the hours and the subject', function () {
    $phrase = (new ReadPhrase)('TP réseaux mardi 14h-17h');

    expect($phrase)->not->toBeNull()
        ->and($phrase->title)->toBe('TP Réseaux')
        ->and($phrase->subject)->toBe('Réseaux')
        ->and($phrase->weekday)->toBe(2)
        ->and($phrase->starts)->toBe('14:00')
        ->and($phrase->ends)->toBe('17:00')
        ->and($phrase->category)->toBe('TD/TP')
        ->and($phrase->weekly)->toBeFalse();
});

test('minutes, a room and a weekly habit are read too', function () {
    $phrase = (new ReadPhrase)('Sport jeudi 8h30-10h salle b12 chaque semaine');

    expect($phrase)->not->toBeNull()
        ->and($phrase->title)->toBe('Sport')
        ->and($phrase->subject)->toBe('')
        ->and($phrase->weekday)->toBe(4)
        ->and($phrase->starts)->toBe('08:30')
        ->and($phrase->ends)->toBe('10:00')
        ->and($phrase->category)->toBe('Sport')
        ->and($phrase->location)->toBe('B12')
        ->and($phrase->weekly)->toBeTrue();
});

test('a clock written with colons is read', function () {
    $phrase = (new ReadPhrase)('cours algo lundi 10:00-12:00');

    expect($phrase)->not->toBeNull()
        ->and($phrase->title)->toBe('Cours Algo')
        ->and($phrase->subject)->toBe('Algo')
        ->and($phrase->weekday)->toBe(1)
        ->and($phrase->starts)->toBe('10:00')
        ->and($phrase->ends)->toBe('12:00')
        ->and($phrase->category)->toBe('Cours');
});

test('an empty phrase prepares nothing', function () {
    expect((new ReadPhrase)('   '))->toBeNull();
});
