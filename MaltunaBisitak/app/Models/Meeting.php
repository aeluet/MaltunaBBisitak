<?php

namespace App\Models;

use Database\Factories\MeetingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
     * @return BelongsToMany<Visitor, $this>
     */
    public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(Visitor::class)->withTimestamps();
    }
}
