<?php

namespace App\Utils\Sql;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtendedBelongsTo extends BelongsTo implements Joinable
{
    /** @var string[] */
    protected array $dependencies = [];

    protected ?Closure $leftJoinDefinition = null;

    public function withJoin(array $dependencies, callable $definition): static
    {
        $this->dependencies = $dependencies;
        $this->leftJoinDefinition = $definition(...);

        return $this;
    }

    public function hasJoin(): bool
    {
        return $this->leftJoinDefinition !== null;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function applyJoin(JoinContext $ctx, SqlName ...$dependencies): void
    {
        $leftJoinExtension = $this->leftJoinDefinition;

        if ($leftJoinExtension === null) {
            throw new Exception('The left join definition is not set. Use `withLeftJoin`');
        }

        $leftJoinExtension($ctx, ...$dependencies);
    }
}
