<?php

namespace App\Utils\Relations;

use App\Utils\Sql\JoinContext;
use App\Utils\Sql\SqlName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

interface Joinable
{
    /**
     * @param  string[]  $dependencies
     * @param  callable(JoinClause $join, string ...$dependencies): void  $definition
     */
    public function withJoin(array $dependencies, callable $definition): static;

    public function hasJoin(): bool;

    /** @return string[] */
    public function getDependencies(): array;

    public function applyJoin(JoinContext $ctx, SqlName ...$dependencies): void;

    /** @return Model */
    public function getRelated();
}
