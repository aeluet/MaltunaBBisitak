<?php

namespace App\Models;

use Database\Factories\ContractorShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['contractor_id', 'checked_in_at', 'checked_out_at', 'needs_key', 'key_returned', 'signature_path'])]
class ContractorShift extends Model
{
    /** @use HasFactory<ContractorShiftFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'needs_key' => 'boolean',
            'key_returned' => 'boolean',
        ];
    }

    /**
     * Contractors currently working at the school.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereNull('checked_out_at');
    }

    /**
     * @return BelongsTo<Contractor, $this>
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
