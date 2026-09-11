<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NumberGenerator
{
    /**
     * Generate a unique prefixed random code for any model table.
     *
     * @param class-string<Model> $modelClass
     */
    public static function generate(
        string $modelClass,
        string $column = 'invoice_number',
        string $prefix = 'INV-',
        int $length = 8
    ): string {
        do {
            // Generate uppercase alphanumeric code (e.g., INV-7K2M9P4X)
            $code = $prefix . strtoupper(Str::random($length));
        } while ($modelClass::where($column, $code)->exists());

        return $code;
    }
}
