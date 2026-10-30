<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractorShiftRequest extends FormRequest
{
    private const string SIGNATURE_PREFIX = 'data:image/png;base64,';

    private const int MAX_SIGNATURE_BYTES = 512 * 1024;

    /**
     * Access is restricted to staff by the route middleware.
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'company' => ['required', 'string', 'max:255'],
            'checked_in_at' => ['required', 'date', 'before_or_equal:now'],
            'needs_key' => ['boolean'],
            'signature' => ['required', 'string', function (string $attribute, mixed $value, Closure $fail): void {
                if (! is_string($value) || $this->decodeSignature($value) === null) {
                    $fail('Sinadura ez da baliozkoa. Sinatu berriro, mesedez.');
                }
            }],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'checked_in_at.before_or_equal' => 'Sarrera ordua ezin da etorkizunekoa izan.',
            'signature.required' => 'Langileak sinatu egin behar du.',
        ];
    }

    /**
     * The validated signature as PNG file contents.
     */
    public function signatureImage(): string
    {
        return $this->decodeSignature($this->validated('signature'));
    }

    /**
     * Decode a canvas data URL, accepting only real PNG images of a reasonable size.
     */
    private function decodeSignature(string $dataUrl): ?string
    {
        if (! str_starts_with($dataUrl, self::SIGNATURE_PREFIX)) {
            return null;
        }

        $contents = base64_decode(substr($dataUrl, strlen(self::SIGNATURE_PREFIX)), true);

        if ($contents === false || strlen($contents) > self::MAX_SIGNATURE_BYTES) {
            return null;
        }

        $imageInfo = rescue(fn () => getimagesizefromstring($contents), false, report: false);

        return is_array($imageInfo) && $imageInfo[2] === IMAGETYPE_PNG ? $contents : null;
    }
}
