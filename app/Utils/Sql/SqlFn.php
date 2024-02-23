<?php

namespace App\Utils\Sql;

use BadMethodCallException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SqlFn
{
    public static function __callStatic(string $name, array $arguments): string
    {
        if (Str::upper($name) !== $name) {
            throw new BadMethodCallException('Call to undefined method '.static::class.'::'."$name().");
        }

        $escaped = array_map(
            fn (mixed $value) => is_a($value, SqlName::class) ? $value : DB::escape($value),
            $arguments
        );

        $args = implode(', ', $escaped);

        return "$name($args)";
    }

    public function __call(string $name, array $arguments): string
    {
        return SqlFn::__call($name, $arguments);
    }
}
