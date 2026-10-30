<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * One visitor's presence in one visit: the check-in / check-out record.
 */
#[Table('visit_visitor')]
class VisitAttendance extends Pivot
{
    use HasUniqueStringIds;

    /**
     * Check-in code characters, without look-alikes such as 0/O or 1/I/L.
     */
    private const string CODE_ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    private const int CODE_LENGTH = 10;

    public $incrementing = true;

    /**
     * The check-in code shown as a QR is generated on insert, like a uuid, so seeded rows get one too.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['code'];
    }

    public function newUniqueId(): string
    {
        return collect(range(1, self::CODE_LENGTH))
            ->map(fn () => self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)])
            ->join('');
    }

    protected function isValidUniqueId($value): bool
    {
        return is_string($value) && preg_match('/^['.self::CODE_ALPHABET.']{'.self::CODE_LENGTH.'}$/', $value) === 1;
    }

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'label_printed_at' => 'datetime',
        ];
    }

    public function checkIn(?string $badgeNumber = null): void
    {
        $this->update(['checked_in_at' => now(), 'badge_number' => $badgeNumber]);
    }

    /**
     * Register the exit and close the visit when this was the last visitor inside.
     */
    public function checkOut(): void
    {
        $this->update(['checked_out_at' => now()]);
        $this->visit->closeIfEveryoneLeft();
    }

    /**
     * Visitors currently inside the school.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereNotNull('checked_in_at')->whereNull('checked_out_at');
    }

    /**
     * @return BelongsTo<Visit, $this>
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * @return BelongsTo<Visitor, $this>
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class)->withTrashed();
    }
}
