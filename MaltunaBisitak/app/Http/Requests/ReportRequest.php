<?php

namespace App\Http\Requests;

use App\Http\Controllers\ReportController;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Filters of the reports page (GET), shared by the on-screen table and the Excel export.
 */
class ReportRequest extends FormRequest
{
    /**
     * A failing GET would otherwise redirect "back" to itself.
     *
     * @var string
     */
    protected $redirectRoute = 'reports.index';

    /**
     * Access is restricted to admins by the route middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mota' => ['nullable', Rule::in(array_keys(ReportController::TYPES))],
            'noiztik' => ['nullable', 'date_format:Y-m-d'],
            'noiz_arte' => ['nullable', 'date_format:Y-m-d', Rule::when($this->filled('noiztik'), 'after_or_equal:noiztik')],
            'jatorria' => ['nullable', 'string', 'max:255'],
            'ostalaria' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'mota' => 'datu mota',
            'noiztik' => 'hasiera data',
            'noiz_arte' => 'amaiera data',
            'jatorria' => 'jatorria',
            'ostalaria' => 'ostalaria',
        ];
    }

    public function type(): string
    {
        return $this->validated('mota') ?? 'bisitariak';
    }

    /**
     * Defaults to the first day of the current month.
     */
    public function from(): CarbonImmutable
    {
        return $this->filled('noiztik')
            ? CarbonImmutable::parse($this->validated('noiztik'))
            : CarbonImmutable::today()->startOfMonth();
    }

    /**
     * Defaults to today.
     */
    public function until(): CarbonImmutable
    {
        return $this->filled('noiz_arte')
            ? CarbonImmutable::parse($this->validated('noiz_arte'))
            : CarbonImmutable::today();
    }
}
