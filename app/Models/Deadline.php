<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeadlineKind;
use Database\Factories\DeadlineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable(['user_id', 'subject_id', 'title', 'kind', 'happens_at', 'location', 'coefficient', 'grade'])]
class Deadline extends Model
{
    /** @use HasFactory<DeadlineFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => DeadlineKind::class,
            'happens_at' => 'datetime',
            'coefficient' => 'decimal:2',
            'grade' => 'decimal:2',
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
     * @param  Builder<Deadline>  $query
     * @return Builder<Deadline>
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function happensAt(): Carbon
    {
        $value = $this->getAttribute('happens_at');

        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        return Carbon::parse(is_string($value) ? $value : 'now');
    }
}
