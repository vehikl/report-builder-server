<?php

namespace App\Utils\Sql;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;

class ExtendedBelongsTo extends BelongsTo
{
    /** @var string[] */
    protected array $leftJoinDependencies = [];

    protected ?Closure $leftJoinDefinition;

    /**
     * @param  string[]  $dependencies
     * @param  callable(JoinClause $join, string ...$dependencies): void  $definition
     */
    public function withLeftJoin(array $dependencies, callable $definition): static
    {
        $this->leftJoinDependencies = $dependencies;
        $this->leftJoinDefinition = $definition(...);

        return $this;
    }

    public function hasLeftJoinDefinition(): bool
    {
        return $this->leftJoinDefinition !== null;
    }

    /** @return string[] */
    public function getLeftJoinDependencies(): array
    {
        return $this->leftJoinDependencies;
    }

    public function applyLeftJoin(JoinClause $join, string ...$dependencies): void
    {
        $leftJoinExtension = $this->leftJoinDefinition;

        if ($leftJoinExtension === null) {
            throw new Exception('The left join definition is not set. Use `withLeftJoin`');
        }

        $leftJoinExtension($join, ...$dependencies);
    }
}
