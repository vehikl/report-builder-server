<?php

namespace App\Utils\Sql;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ExtendedAttribute extends Attribute
{
    /** @var string[] */
    protected array $sqlDependencies = [];

    protected ?Closure $sqlDefinition = null;

    /**
     * @param  string[]  $dependencies
     * @param  callable(string ...$dependencies): string  $definition
     */
    public function withSql(array $dependencies, callable $definition): static
    {
        $this->sqlDependencies = $dependencies;
        $this->sqlDefinition = $definition;

        return $this;
    }

    public function hasSqlDefinition(): bool
    {
        return $this->sqlDefinition !== null;
    }

    /** @return string[] */
    public function getSqlDependencies(): array
    {
        return $this->sqlDependencies;
    }

    public function toSql(string ...$dependencies): string
    {
        $factory = $this->sqlDefinition;

        if ($factory === null) {
            throw new Exception('The SQL definition is not set. Use `withSql`');
        }

        return $factory(...$dependencies);
    }
}
