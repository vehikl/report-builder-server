<?php

namespace App\Utils\Expressions;

use App\Utils\Sql\FunctionDefinition;
use App\Utils\Sql\SqlName;

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

    /** @param  array<string, FunctionDefinition>  $functions */
    public function toSql(ExpressionContext $ctx): string
    {
        $args = array_map(fn (Expression $arg) => SqlName::make($arg->toSql($ctx)), $this->args);

        return $ctx->functions[$this->identifier]->toSql(...$args);
    }
}
