<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * One visitor's presence in one visit: the check-in / check-out record.
 */
#[Table('visit_visitor')]
class VisitAttendance extends Pivot
{
    public $incrementing = true;

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
