<?php

if (! function_exists('mask_ic_passport')) {
    /**
     * Mask a displayed IC/passport value while retaining its final four characters.
     * Keep the original value for persistence, validation, matching, and searches.
     */
    function mask_ic_passport($value, string $empty = '-'): string
    {
        $value = trim((string) $value);
        if ($value === '' || strtoupper($value) === 'N/A' || strtoupper($value) === 'NULL') {
            return $empty;
        }

        return 'XXXX' . substr($value, -4);
    }
}
