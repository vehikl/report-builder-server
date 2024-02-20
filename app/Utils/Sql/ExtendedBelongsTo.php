<?php

namespace App\Utils\Sql;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;

class ExtendedBelongsTo extends BelongsTo implements LeftJoinable
{
    /** @var string[] */
    protected array $dependencies = [];

    protected ?Closure $leftJoinDefinition = null;

    public function withLeftJoin(array $dependencies, callable $definition): static
    {
        $this->dependencies = $dependencies;
        $this->leftJoinDefinition = $definition(...);

        return $this;
    }

    public function hasLeftJoinDefinition(): bool
    {
        return $this->leftJoinDefinition !== null;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function applyLeftJoin(JoinClause $join, SqlName ...$dependencies): void
    {
        $leftJoinExtension = $this->leftJoinDefinition;

        if ($leftJoinExtension === null) {
            throw new Exception('The left join definition is not set. Use `withLeftJoin`');
        }

        $leftJoinExtension($join, ...$dependencies);
    }
}
