<?php

namespace App\Http\Requests;

use App\Models\Visitor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisitorRequest extends FormRequest
{
    /**
     * Access is restricted to staff by the route middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise the document number so "12345678 z" and "12345678Z" are the same person.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('document_number')) {
            $this->merge([
                'document_number' => strtoupper(preg_replace('/[\s-]+/', '', $this->input('document_number'))),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:20', Rule::unique('visitors')->ignore($this->route('visitor'))],
            'origin' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', Rule::in(array_keys(Visitor::countryOptions()))],
            'phone' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_number.unique' => 'NAN zenbaki hori duen bisitaria badago jada.',
        ];
    }
}
