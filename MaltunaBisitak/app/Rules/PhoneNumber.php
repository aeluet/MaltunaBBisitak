<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A phone number as people usually write it: digits with optional spaces, dots, dashes
 * or parentheses, and an optional leading "+" for the country code ("611 22 23 33",
 * "943-12-34-56", "+34 611 222 333"). It must hold between 9 and 15 digits (E.164).
 */
class PhoneNumber implements ValidationRule
{
    public const int MIN_DIGITS = 9;

    public const int MAX_DIGITS = 15;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! self::isValid($value)) {
            $fail('validation.phone')->translate();
        }
    }

    public static function isValid(string $value): bool
    {
        $compact = preg_replace('/[\s.()\-]/u', '', trim($value));

        return preg_match('/^\+?\d{'.self::MIN_DIGITS.','.self::MAX_DIGITS.'}$/', $compact) === 1;
    }
}
