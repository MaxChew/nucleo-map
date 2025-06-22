<?php

namespace Maxxidev\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPassword implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! preg_match('/[@$!%*#?&]/', $value)) {
            return false;
        }

        if (! preg_match('/[A-Z]/', $value)) {
            return false;
        }

        if (! preg_match('/[A-Za-z]/', $value)) {
            return false;
        }

        // fail if no digit
        if (! preg_match('/[0-9]/', $value)) {
            return false;
        }

        // fail if got white space
        if (preg_match('/\s/', $value)) {
            return false;
        }

        if (mb_strlen($value) < 8) {
            return false;
        }

        // pass if length >= 8
        return mb_strlen($value) <= 16;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Your :attribute must be between 8 to 16 characters, at least one uppercase, numeric & special characters (@$!%*#?&)';
    }
}
