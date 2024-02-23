<?php

namespace App\Utils\Sql;

use Closure;

class FunctionDefinition
{
    public readonly Closure $sqlDefinition;

    /** @param  callable(SqlName ...$args): string  $sql */
    public function __construct(callable $sql)
    {
        $this->sqlDefinition = $sql(...);
    }

    public function toSql(SqlName ...$args): string
    {
        $sqlDefinition = $this->sqlDefinition;

        return $sqlDefinition(...$args);
    }
}
