<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VisitTypeRequest extends FormRequest
{
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
            'name_eu' => ['required', 'string', 'max:255'],
            'name_es' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name_eu' => 'izena (euskaraz)',
            'name_es' => 'izena (gaztelaniaz)',
            'name_en' => 'izena (ingelesez)',
            'sort_order' => 'ordena',
        ];
    }
}
