<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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

    /**
     * ISO 3166 country codes with their names in the application locale, sorted by name.
     *
     * @return array<string, string>
     */
    public static function countryOptions(): array
    {
        return once(function (): array {
            $countries = [];

            foreach (ResourceBundle::create(app()->getLocale(), 'ICUDATA-region')['Countries'] as $code => $name) {
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
