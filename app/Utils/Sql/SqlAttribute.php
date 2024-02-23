<?php

namespace App\Utils\Sql;

use Closure;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SqlAttribute extends Attribute
{
    /** @var string[] */
    protected readonly array $dependencies;

    protected readonly ?Closure $sqlDefinition;

    /**
     * @param  string[]  $dependencies
     * @param  callable(SqlName ...$names): string  $sql
     */
    public function __construct(array $dependencies, callable $sql, ?callable $get = null, ?callable $set = null)
    {
        parent::__construct($get, $set);

        $this->dependencies = $dependencies;
        $this->sqlDefinition = $sql(...);
    }

    /**
     * @param  string[]  $dependencies
     * @param  callable(SqlName ...$names): string  $sql
     */
    public static function new(array $dependencies, callable $sql, callable $get, ?callable $set = null): static
    {
        return new self($dependencies, $sql, $get, $set);
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function toSql(SqlName ...$names): string
    {
        $sqlDefinition = $this->sqlDefinition;

        return $sqlDefinition(...$names);
    }
}
