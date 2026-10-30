<?php

namespace App\Http\Requests;

use App\Http\Controllers\BookingController;
use App\Models\AvailableDate;
use App\Models\Visitor;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * The public form is open to everyone (WP001); abuse is limited by throttling and a honeypot.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Required data follows WP004–WP005: a visit type, the day, where they come from, the group size
     * for groups and at least one visitor, whose email is needed to answer (WP008).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'types' => ['required_without:other_type', 'array'],
            'types.*' => ['integer', Rule::exists('visit_types', 'id')->where('is_active', true)],
            'other_type' => ['nullable', 'string', 'max:255'],
            'visit_date' => ['bail', 'required', 'date_format:Y-m-d', 'after:today', $this->ensureDateIsBookable(...)],
            'origin' => ['required', 'string', 'max:255'],
            'is_group' => ['required', 'boolean'],
            'group_size' => ['exclude_unless:is_group,1', 'required', 'integer', 'min:2', 'max:500'],
            'visitors' => ['required', 'array', 'min:1', 'max:'.BookingController::MAX_VISITORS],
            'visitors.0.email' => ['required'],
            'visitors.*.first_name' => ['required', 'string', 'max:255'],
            'visitors.*.last_name' => ['required', 'string', 'max:255'],
            'visitors.*.email' => ['nullable', 'email', 'max:255'],
            'visitors.*.country' => ['required', Rule::in(array_keys(Visitor::countryOptions()))],
            'notes' => ['nullable', 'string', 'max:2000'],
            'privacy' => ['accepted'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'types.required_without' => __('public.validation.type_required'),
            'visit_date.required' => __('public.validation.date_required'),
            'visit_date.date_format' => __('public.validation.date_unavailable'),
            'visit_date.after' => __('public.validation.date_unavailable'),
            'visitors.0.email.required' => __('public.validation.contact_email_required'),
            'privacy.accepted' => __('public.validation.privacy'),
        ];
    }

    private function ensureDateIsBookable(string $attribute, mixed $value, Closure $fail): void
    {
        $availableDate = AvailableDate::whereDate('date', $value)->first();

        if (! $availableDate) {
            $fail(__('public.validation.date_unavailable'));
        } elseif ($availableDate->isFull()) {
            $fail(__('public.validation.date_full'));
        }
    }
}
