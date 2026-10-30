<?php

namespace App\Models;

use Database\Factories\VisitTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name_eu', 'name_es', 'name_en', 'sort_order', 'is_active'])]
class VisitType extends Model
{
    /** @use HasFactory<VisitTypeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function nameFor(string $locale): string
    {
        return $this->{"name_{$locale}"} ?? $this->name_eu;
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * @return BelongsToMany<Visit, $this>
     */
    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class);
    }
}
