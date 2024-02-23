<?php

namespace App\Utils\Expressions;

class GroupExpression extends Expression
{
    public function __construct(public readonly Expression $expression)
    {
    }

    public function getFieldPaths(): array
    {
        return $this->expression->getFieldPaths();
    }

    public function toArray(): array
    {
        return [
            'type' => 'group',
            'expression' => $this->expression->toArray(),
        ];
    }

    public function toSql(array $sqlNames): string
    {
        return "({$this->expression->toSql($sqlNames)})";
    }
}
