<?php

declare(strict_types=1);

namespace App\Livewire\Revision;

use App\Actions\Revision\SaveRevision;
use App\Enums\RevisionStage;
use App\Livewire\Concerns\ResolvesUser;
use App\Models\RevisionItem;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RevisionBoard extends Component
{
    use ResolvesUser;

    public string $title = '';

    public string $subjectId = '';

    public function save(SaveRevision $save): void
    {
        $user = $this->user();
        $this->authorize('create', RevisionItem::class);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'subjectId' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')->where(fn ($query) => $query->where('user_id', $user->id)),
            ],
        ]);

        $subject = Subject::query()->forUser($user)->find((int) $validated['subjectId']);

        if (! $subject instanceof Subject) {
            abort(404);
        }

        $save($user, $subject, $validated['title']);
        $this->reset('title');
    }

    public function advance(int $itemId): void
    {
        $item = $this->owned($itemId);
        $this->authorize('update', $item);
        $item->fill(['stage' => $item->stage()->next()]);
        $item->save();
    }

    public function confidence(int $itemId, int $level): void
    {
        if ($level < 1 || $level > 5) {
            return;
        }

        $item = $this->owned($itemId);
        $this->authorize('update', $item);
        $item->confidence = $level;
        $item->save();
    }

    public function remove(int $itemId): void
    {
        $item = $this->owned($itemId);
        $this->authorize('delete', $item);
        $item->delete();
    }

    public function render(): View
    {
        $rows = [];

        foreach (RevisionItem::query()->forUser($this->user())->with('subject')->orderBy('id')->get() as $item) {
            $subject = $item->subject;
            $rows[] = [
                'id' => $item->id,
                'title' => $item->title,
                'subject' => $subject instanceof Subject ? $subject->name : '',
                'stage' => $this->stageLabel($item->stage()),
                'confidence' => $item->confidence,
            ];
        }

        return view('livewire.revision.revision-board', [
            'rows' => $rows,
            'subjects' => Subject::query()->forUser($this->user())->orderBy('name')->get(),
        ]);
    }

    private function owned(int $itemId): RevisionItem
    {
        $item = RevisionItem::query()->forUser($this->user())->find($itemId);

        if (! $item instanceof RevisionItem) {
            abort(404);
        }

        return $item;
    }

    private function stageLabel(mixed $stage): string
    {
        $value = $stage instanceof RevisionStage ? $stage->value : 'reading';

        return match ($value) {
            'notes' => __('Notes'),
            'exercises' => __('Exercises'),
            'final' => __('Final revision'),
            default => __('Reading'),
        };
    }
}
