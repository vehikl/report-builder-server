<?php

namespace App\Utils\Sql;

use Illuminate\Database\Query\JoinClause;

interface LeftJoinable
{
    /**
     * @param  string[]  $dependencies
     * @param  callable(JoinClause $join, string ...$dependencies): void  $definition
     */
    public function withLeftJoin(array $dependencies, callable $definition): static;

    public function hasLeftJoinDefinition(): bool;

    /** @return string[] */
    public function getLeftJoinDependencies(): array;

    public function applyLeftJoin(JoinClause $join, string ...$dependencies): void;
}
