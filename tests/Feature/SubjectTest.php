<?php

use App\Livewire\Subject\SubjectManager;
use App\Models\Subject;
use App\Models\User;
use Livewire\Livewire;

test('a student saves a subject and cannot touch another one', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $theirs = Subject::factory()->for($other)->create(['name' => 'Secret']);

    $this->actingAs($user);

    Livewire::test(SubjectManager::class)
        ->set('name', 'Réseaux')
        ->set('teacher', 'M. Koffi')
        ->set('room', 'B12')
        ->set('coefficient', '3')
        ->call('save')
        ->assertHasNoErrors();

    $subject = Subject::query()->where('user_id', $user->id)->where('name', 'Réseaux')->first();

    expect($subject)->not->toBeNull()
        ->and($subject->teacher)->toBe('M. Koffi')
        ->and($subject->default_room)->toBe('B12');

    Livewire::test(SubjectManager::class)
        ->call('remove', $theirs->id)
        ->assertNotFound();

    expect(Subject::query()->find($theirs->id))->not->toBeNull();
});
