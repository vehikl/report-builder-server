<?php

namespace App\Utils\Relations;

use App\Utils\Sql\JoinContext;
use App\Utils\Sql\SqlName;
use Closure;
use Exception;

trait IsJoinable
{
    /** @var string[] */
    protected array $dependencies = [];

    protected ?Closure $joinDefinition = null;

    public function withJoin(array $dependencies, callable $definition): static
    {
        $this->dependencies = $dependencies;
        $this->joinDefinition = $definition(...);

        return $this;
    }

    public function hasJoin(): bool
    {
        return $this->joinDefinition !== null;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function applyJoin(JoinContext $ctx, SqlName ...$dependencies): void
    {
        $leftJoinExtension = $this->joinDefinition;

        if ($leftJoinExtension === null) {
            throw new Exception('The join definition is not set. Use `withJoin`');
        }

        $leftJoinExtension($ctx, ...$dependencies);
    }
}
