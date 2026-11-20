<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable(['user_id', 'subject_id', 'title', 'priority', 'status', 'due_on', 'estimate_minutes'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
            'due_on' => 'date',
            'estimate_minutes' => 'integer',
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
     * @return HasMany<Subtask, $this>
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function priority(): TaskPriority
    {
        return $this->priorityFrom($this->priority);
    }

    public function status(): TaskStatus
    {
        return $this->statusFrom($this->status);
    }

    public function dueOn(): ?Carbon
    {
        return $this->day($this->due_on);
    }

    private function priorityFrom(mixed $value): TaskPriority
    {
        if ($value instanceof TaskPriority) {
            return $value;
        }

        if (is_string($value)) {
            return TaskPriority::tryFrom($value) ?? TaskPriority::B;
        }

        return TaskPriority::B;
    }

    private function statusFrom(mixed $value): TaskStatus
    {
        if ($value instanceof TaskStatus) {
            return $value;
        }

        if (is_string($value)) {
            return TaskStatus::tryFrom($value) ?? TaskStatus::Todo;
        }

        return TaskStatus::Todo;
    }

    private function day(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value);
        }

        return null;
    }
}
