<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Locale;
use ResourceBundle;

#[Fillable(['first_name', 'last_name', 'email', 'origin', 'country', 'document_number', 'phone', 'notes'])]
class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory, SoftDeletes;

    /**
     * ICU region codes that are not countries.
     */
    private const array NON_COUNTRY_REGIONS = ['EU', 'EZ', 'QO', 'UN', 'XA', 'XB', 'ZZ'];

    private const array SEARCHABLE_COLUMNS = ['first_name', 'last_name', 'email', 'document_number', 'origin'];

    /**
     * "12345678 z" and "12345678-Z" are the same document as "12345678Z".
     */
    public static function normalizeDocumentNumber(string $documentNumber): string
    {
        return strtoupper(preg_replace('/[\s-]+/', '', $documentNumber));
    }

    /**
     * ISO 3166 country codes with their names in the application locale, sorted by name.
     *
     * @return array<string, string>
     */
    public static function countryOptions(): array
    {
        return self::countriesIn(app()->getLocale());
    }

    /**
     * @return array<string, string>
     */
    private static function countriesIn(string $locale): array
    {
        return once(function () use ($locale): array {
            $countries = [];

            foreach (ResourceBundle::create($locale, 'ICUDATA-region')['Countries'] as $code => $name) {
                if (preg_match('/^[A-Z]{2}$/', $code) && ! in_array($code, self::NON_COUNTRY_REGIONS, true)) {
                    $countries[$code] = $name;
                }
            }

            asort($countries, SORT_LOCALE_STRING);

            return $countries;
        });
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function countryName(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->country
            ? Locale::getDisplayRegion("-{$this->country}", app()->getLocale())
            : null);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => "{$this->first_name} {$this->last_name}");
    }

    /**
     * Reuse a visitor only when email and names all match, so a public form never edits someone else's record.
     *
     * @param  array{first_name: string, last_name: string, email?: string|null}  $attributes
     */
    public static function matchOrCreate(array $attributes): self
    {
        $existing = filled($attributes['email'] ?? null)
            ? static::where('email', $attributes['email'])
                ->where('first_name', $attributes['first_name'])
                ->where('last_name', $attributes['last_name'])
                ->first()
            : null;

        return $existing ?? static::create($attributes);
    }

    /**
     * Every word must match a name, email, document number or origin, so "ane zubi" finds Ane Zubiaurre.
     */
    #[Scope]
    protected function search(Builder $query, string $term): void
    {
        foreach (preg_split('/\s+/', trim($term), flags: PREG_SPLIT_NO_EMPTY) as $word) {
            $query->where(function (Builder $query) use ($word): void {
                foreach (self::SEARCHABLE_COLUMNS as $column) {
                    $query->orWhere($column, 'like', "%{$word}%");
                }
            });
        }
    }

    /**
     * @return BelongsToMany<Visit, $this, VisitAttendance, 'attendance'>
     */
    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class)
            ->using(VisitAttendance::class)
            ->as('attendance')
            ->withPivot(['id', 'code', 'checked_in_at', 'checked_out_at', 'badge_number', 'label_printed_at'])
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
