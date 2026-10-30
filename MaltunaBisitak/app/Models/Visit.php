<?php

namespace App\Models;

use App\Enums\VisitStatus;
use Database\Factories\VisitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['visit_date', 'origin', 'is_group', 'group_size', 'other_type', 'status', 'locale', 'host_id', 'notes'])]
class Visit extends Model
{
    /** @use HasFactory<VisitFactory> */
    use HasFactory, HasUuids;

    protected $attributes = [
        'status' => 'pending',
        'locale' => 'eu',
        'is_group' => false,
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'is_group' => 'boolean',
            'status' => VisitStatus::class,
            'reviewed_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * The uuid is only exposed in QR codes and public links; the numeric id stays the primary key.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * @return BelongsToMany<Visitor, $this, VisitAttendance, 'attendance'>
     */
    public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(Visitor::class)
            ->using(VisitAttendance::class)
            ->as('attendance')
            ->withPivot(['id', 'checked_in_at', 'checked_out_at', 'badge_number', 'label_printed_at'])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<VisitType, $this>
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(VisitType::class);
    }
}
