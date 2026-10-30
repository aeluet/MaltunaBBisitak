<?php

namespace App\Models;

use Database\Factories\MeetingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'location', 'date', 'starts_at'])]
class Meeting extends Model
{
    /** @use HasFactory<MeetingFactory> */
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'date' => 'date',
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
     * MySQL returns TIME columns as "10:30:00" and SQLite as "10:30"; always expose "10:30".
     *
     * @return Attribute<string|null, never>
     */
    protected function startsAt(): Attribute
    {
        return Attribute::get(fn (?string $value): ?string => $value ? substr($value, 0, 5) : null);
    }

    /**
     * @return BelongsToMany<Visitor, $this>
     */
    public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(Visitor::class)->withTimestamps();
    }
}
