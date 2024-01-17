<?php

namespace App\Utils\Expressions;

use App\Utils\Environment;
use Exception;
use Illuminate\Support\Collection;

class CallExpression extends Expression
{
    /** @var Expression[] */
    public readonly array $args;

    public function __construct(
        public readonly string $identifier,
        Expression ...$args,
    ) {
        $this->args = $args;
    }

    public function getDbPaths(int $entityId, Collection $attributes): array
    {
        return array_merge(...array_map(
            fn (Expression $expression) => $expression->getDbPaths($entityId, $attributes),
            $this->args
        ));
    }

    public function evaluate(Environment $environment): mixed
    {
        $function = $environment->findFunction($this->identifier);

        if (! is_callable($function)) {
            throw new Exception("Not a function: {$this->identifier}");
        }

        $evaluatedArgs = array_map(fn (Expression $arg) => $arg->evaluate($environment), $this->args);

        return $function(...$evaluatedArgs);
    }
}