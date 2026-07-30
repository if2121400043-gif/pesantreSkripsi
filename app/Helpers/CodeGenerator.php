<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class CodeGenerator
{
    /**
     * Generate a unique acronym/code from a name string for a specified table and column.
     *
     * Examples:
     * - "Wilayah Sunan Giri" -> "WSG" (or "WSG-01" if WSG exists)
     * - "Asrama Al-Farabi" -> "AAF"
     * - "Bahasa Indonesia" -> "BIN"
     * - "Matematika" -> "MATE"
     *
     * @param string $name Target name string
     * @param string $table Database table name
     * @param string $column Database column name (default: 'kode')
     * @param int|null $ignoreId ID to ignore for unique checks during update
     * @param string $prefix Optional prefix (e.g., 'KMR-')
     * @return string Unique code string
     */
    public static function generate(string $name, string $table, string $column = 'kode', ?int $ignoreId = null, string $prefix = ''): string
    {
        $cleanName = trim(preg_replace('/[^\w\s]/', '', $name));
        $words = array_values(array_filter(explode(' ', $cleanName)));
        
        $acronym = '';
        if (count($words) >= 2) {
            foreach ($words as $w) {
                if (!empty($w)) {
                    $acronym .= strtoupper(substr($w, 0, 1));
                }
            }
        } elseif (count($words) === 1) {
            $single = $words[0];
            $acronym = strtoupper(substr($single, 0, min(4, strlen($single))));
        } else {
            $acronym = 'KODE';
        }

        $baseCode = strtoupper($prefix . $acronym);
        $candidate = $baseCode;
        $counter = 1;

        while (self::exists($table, $column, $candidate, $ignoreId)) {
            $candidate = $baseCode . '-' . sprintf('%02d', $counter);
            $counter++;
        }

        return $candidate;
    }

    private static function exists(string $table, string $column, string $code, ?int $ignoreId = null): bool
    {
        $query = DB::table($table)->where($column, $code);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        return $query->exists();
    }
}
