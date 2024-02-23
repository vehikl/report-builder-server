<?php

namespace App\Utils\Expressions;

class StringExpression extends Expression
{
    public function __construct(public readonly string $value)
    {
    }

    public function getFieldPaths(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'type' => 'string',
            'value' => $this->value,
        ];
    }

    public function toSql(array $sqlNames): string
    {
        return "'$this->value'";
    }
}
