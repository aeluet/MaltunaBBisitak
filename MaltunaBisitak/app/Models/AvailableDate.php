<?php

namespace App\Models;

use Database\Factories\AvailableDateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['date', 'max_visits', 'notes'])]
class AvailableDate extends Model
{
    /** @use HasFactory<AvailableDateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'max_visits' => 'integer',
        ];
    }

    /**
     * Whether the date reached its visit limit. Pass the scheduled count when it is already known.
     */
    public function isFull(?int $scheduledVisits = null): bool
    {
        if ($this->max_visits === null) {
            return false;
        }

        $scheduledVisits ??= Visit::whereDate('visit_date', $this->date)
            ->whereIn('status', Visit::SCHEDULED_STATUSES)
            ->count();

        return $scheduledVisits >= $this->max_visits;
    }
}
