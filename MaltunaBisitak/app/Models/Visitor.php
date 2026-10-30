<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['first_name', 'last_name', 'email', 'origin', 'country', 'document_number', 'phone', 'notes'])]
class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => "{$this->first_name} {$this->last_name}");
    }

    /**
     * @return BelongsToMany<Visit, $this, VisitAttendance, 'attendance'>
     */
    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class)
            ->using(VisitAttendance::class)
            ->as('attendance')
            ->withPivot(['id', 'checked_in_at', 'checked_out_at', 'badge_number', 'label_printed_at'])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Meeting, $this>
     */
    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class)->withTimestamps();
    }
}
