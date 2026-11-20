<?php

use App\Livewire\Task\TaskBoard;
use App\Models\StudySession;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

test('a student adds a task, a step, and a real 25 minutes', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $secret = Task::factory()->for($other)->create(['title' => 'Secret']);

    $this->actingAs($user);

    Livewire::test(TaskBoard::class)
        ->set('title', 'Fiche réseaux')
        ->set('priority', 'A')
        ->set('subjectName', 'Réseaux')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Fiche réseaux')
        ->assertDontSee('Secret');

    $task = Task::query()->where('title', 'Fiche réseaux')->firstOrFail();

    expect($task->subject?->name)->toBe('Réseaux');

    Livewire::test(TaskBoard::class)
        ->call('open', $task->id)
        ->set('step', 'Relire le cours')
        ->call('addStep', $task->id)
        ->call('note', $task->id)
        ->call('remove', $secret->id)
        ->assertNotFound();

    expect(Subtask::query()->where('title', 'Relire le cours')->exists())->toBeTrue()
        ->and(StudySession::query()->where('task_id', $task->id)->value('minutes'))->toBe(25)
        ->and(Task::query()->find($secret->id))->not->toBeNull();
});
