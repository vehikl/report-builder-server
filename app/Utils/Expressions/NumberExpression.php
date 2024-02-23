<?php

namespace App\Utils\Expressions;

use Exception;

class NumberExpression extends Expression
{
    public function __construct(public readonly string $value)
    {
        if (! is_numeric($this->value)) {
            throw new Exception("Not a number: $this->value");
        }
    }

    public function getFieldPaths(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'type' => 'number',
            'value' => $this->value,
        ];
    }

    public function toSql(ExpressionContext $ctx): string
    {
        return $this->value;
    }
}
