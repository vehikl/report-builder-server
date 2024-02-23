<?php

namespace App\Utils\Expressions;

use Exception;

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

    public function getFieldPaths(): array
    {
        return array_merge(...array_map(
            fn (Expression $expression) => $expression->getFieldPaths(),
            $this->args
        ));
    }

    public function toArray(): array
    {
        return [
            'type' => 'call',
            'fn' => $this->identifier,
            'args' => array_map(fn (Expression $expression) => $expression->toArray(), $this->args),
        ];
    }

    public function toSql(array $sqlNames): string
    {
        // TODO: implement
        throw new Exception('Now allowed for now');
    }
}
