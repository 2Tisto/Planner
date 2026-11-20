<?php

use App\Livewire\Review\ReviewBoard;
use App\Livewire\Revision\RevisionBoard;
use App\Models\RevisionItem;
use App\Models\Subject;
use App\Models\User;
use App\Models\WeeklyReview;
use Livewire\Livewire;

test('a chapter moves forward and the week can be written down', function () {
    $user = User::factory()->create();
    $subject = Subject::factory()->for($user)->create(['name' => 'Algo']);
    $this->actingAs($user);

    Livewire::test(RevisionBoard::class)
        ->set('subjectId', (string) $subject->id)
        ->set('title', 'Graphes')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Lecture')
        ->call('advance', RevisionItem::query()->where('title', 'Graphes')->firstOrFail()->id)
        ->assertSee('Fiches')
        ->call('confidence', RevisionItem::query()->where('title', 'Graphes')->firstOrFail()->id, 4);

    expect(RevisionItem::query()->where('title', 'Graphes')->firstOrFail()->confidence)->toBe(4);

    Livewire::test(ReviewBoard::class)
        ->set('wins', 'Le DS est prêt')
        ->set('satisfaction', '4')
        ->call('save')
        ->assertHasNoErrors();

    expect(WeeklyReview::query()->where('user_id', $user->id)->firstOrFail()->wins)->toBe('Le DS est prêt');
});
