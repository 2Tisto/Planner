<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RevisionStage;
use Database\Factories\RevisionItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'subject_id', 'title', 'stage', 'confidence'])]
class RevisionItem extends Model
{
    /** @use HasFactory<RevisionItemFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stage' => RevisionStage::class,
            'confidence' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @param  Builder<RevisionItem>  $query
     * @return Builder<RevisionItem>
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function stage(): RevisionStage
    {
        return $this->stageFrom($this->stage);
    }

    private function stageFrom(mixed $value): RevisionStage
    {
        if ($value instanceof RevisionStage) {
            return $value;
        }

        if (is_string($value)) {
            return RevisionStage::tryFrom($value) ?? RevisionStage::Reading;
        }

        return RevisionStage::Reading;
    }
}
