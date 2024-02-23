<?php

namespace App\Utils\Expressions;

class IdentifierExpression extends Expression
{
    public function __construct(public readonly string $identifier)
    {
    }

    public function getFieldPaths(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'type' => 'identifier',
            'value' => $this->identifier,
        ];
    }

    public function toSql(ExpressionContext $ctx): string
    {
        return $ctx->getValue($this->identifier);
    }
}
