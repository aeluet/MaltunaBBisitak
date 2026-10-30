<?php

namespace App\Models;

use Database\Factories\ContractorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['first_name', 'last_name', 'phone', 'company'])]
class Contractor extends Model
{
    /** @use HasFactory<ContractorFactory> */
    use HasFactory;

    /**
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => "{$this->first_name} {$this->last_name}");
    }

    /**
     * @return HasMany<ContractorShift, $this>
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(ContractorShift::class);
    }
}
