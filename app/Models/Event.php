<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'user_id',
    'category_id',
    'subject_id',
    'title',
    'location',
    'notes',
    'starts_at',
    'ends_at',
    'timezone',
    'is_all_day',
    'recurrence_frequency',
    'recurrence_interval',
    'recurrence_until',
])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_all_day' => 'boolean',
            'recurrence_interval' => 'integer',
            'recurrence_until' => 'date',
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
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @param  Builder<Event>  $query
     * @return Builder<Event>
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function subjectLabel(): string
    {
        $subject = $this->getRelationValue('subject');

        if (! $subject instanceof Subject) {
            return '';
        }

        return $subject->name;
    }

    public function beginsAt(): Carbon
    {
        return $this->moment($this->starts_at);
    }

    public function finishesAt(): Carbon
    {
        return $this->moment($this->ends_at);
    }

    private function moment(mixed $value): Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_string($value)) {
            return Carbon::parse($value);
        }

        throw new \LogicException('An event needs its date.');
    }
}
