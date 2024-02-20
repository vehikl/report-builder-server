<?php

namespace App\Utils\Sql;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ExtendedAttribute extends Attribute
{
    /** @var string[] */
    protected array $dependencies = [];

    protected ?Closure $definition = null;

    /**
     * @param  string[]  $dependencies
     * @param  callable(SqlName ...$dependencies): string  $definition
     */
    public function withSql(array $dependencies, callable $definition): static
    {
        $this->dependencies = $dependencies;
        $this->definition = $definition;

        return $this;
    }

    public function hasSqlDefinition(): bool
    {
        return $this->definition !== null;
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function toSql(SqlName ...$dependencies): string
    {
        $factory = $this->definition;

        if ($factory === null) {
            throw new Exception('The SQL definition is not set. Use `withSql`');
        }

        return $factory(...$dependencies);
    }
}
