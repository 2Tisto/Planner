<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'event_id',
    'user_id',
    'original_date',
    'type',
    'starts_at',
    'ends_at',
    'title',
    'location',
    'notes',
    'category_id',
    'subject_id',
])]
class EventException extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'original_date' => 'date',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
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

    public function originalDate(): string
    {
        return $this->dateString($this->original_date);
    }

    public function beginsAt(): ?Carbon
    {
        if ($this->starts_at === null) {
            return null;
        }

        return $this->moment($this->starts_at);
    }

    public function finishesAt(): ?Carbon
    {
        if ($this->ends_at === null) {
            return null;
        }

        return $this->moment($this->ends_at);
    }

    public function subjectLabel(): string
    {
        $subject = $this->getRelationValue('subject');

        if (! $subject instanceof Subject) {
            return '';
        }

        return $subject->name;
    }

    public static function remember(Event $event, string $date): self
    {
        $existing = self::query()
            ->where('event_id', $event->id)
            ->whereDate('original_date', $date)
            ->first();

        if ($existing instanceof self) {
            return $existing;
        }

        $exception = new self;
        $exception->fill([
            'event_id' => $event->id,
            'user_id' => $event->user_id,
            'original_date' => $date,
            'type' => 'overridden',
        ]);

        return $exception;
    }

    private function dateString(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->toDateString();
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value)->toDateString();
        }

        return '';
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

        throw new \LogicException('An exception needs its date.');
    }
}
