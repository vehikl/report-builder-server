<?php

namespace App\Utils\Sql;

use Illuminate\Support\Facades\DB;

class SqlFn
{
    public static function __callStatic(string $name, array $arguments): string
    {
        $escaped = array_map(
            fn (mixed $value) => is_a($value, SqlName::class) ? $value : DB::escape($value),
            $arguments
        );

        $args = implode(', ', $escaped);

        return "$name($args)";
    }
}
