<?php

namespace App\Http\Requests;

use App\Models\VisitAttendance;
use App\Models\Visitor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreWalkInRequest extends FormRequest
{
    /**
     * Fields used to register a visitor who is not in the system yet.
     */
    public const array NEW_VISITOR_FIELDS = ['first_name', 'last_name', 'document_number', 'origin', 'phone', 'email'];

    /**
     * Access is restricted to staff by the route middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('document_number')) {
            $this->merge([
                'document_number' => Visitor::normalizeDocumentNumber($this->input('document_number')),
            ]);
        }
    }

    /**
     * Either an existing visitor_id or the fields of a new visitor.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'visitor_id' => ['nullable', 'integer', Rule::exists('visitors', 'id')->whereNull('deleted_at')],
            'first_name' => ['exclude_with:visitor_id', 'required', 'string', 'max:255'],
            'last_name' => ['exclude_with:visitor_id', 'required', 'string', 'max:255'],
            'document_number' => ['exclude_with:visitor_id', 'nullable', 'string', 'max:20', Rule::unique('visitors')],
            'origin' => ['exclude_with:visitor_id', 'nullable', 'string', 'max:255'],
            'phone' => ['exclude_with:visitor_id', 'nullable', 'string', 'max:20'],
            'email' => ['exclude_with:visitor_id', 'nullable', 'email', 'max:255'],
            'host_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'badge_number' => [
                'nullable', 'string', 'max:20',
                Rule::unique('visit_visitor')->whereNotNull('checked_in_at')->whereNull('checked_out_at'),
            ],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->filled('visitor_id') && VisitAttendance::active()->where('visitor_id', $this->integer('visitor_id'))->exists()) {
                    $validator->errors()->add('visitor_id', 'Bisitari hau ikastetxean dago jada.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_number.unique' => 'NAN zenbaki hori duen bisitaria badago jada: bilatu eta hautatu.',
            'badge_number.unique' => 'Txartel hori beste bisitari batek du une honetan.',
        ];
    }
}
