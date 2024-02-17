<?php

namespace App\Utils\Sql;

class SqlFn
{
    public static function __callStatic(string $name, array $arguments): string
    {
        $args = implode(', ', $arguments);

        return "$name($args)";
    }
}
