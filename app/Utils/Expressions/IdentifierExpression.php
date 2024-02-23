<?php

namespace App\Utils\Expressions;

use Exception;

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
        // TODO: implement
        throw new Exception('Now allowed for now');
    }
}
