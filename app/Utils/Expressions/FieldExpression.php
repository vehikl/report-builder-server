<?php

namespace App\Utils\Expressions;

class FieldExpression extends Expression
{
    public function __construct(public readonly string $path)
    {
    }

    public function getFieldPaths(): array
    {
        return [$this->path];
    }

    public function toArray(): array
    {
        return [
            'type' => 'field',
            'value' => $this->path,
        ];
    }

    public function toSql(ExpressionContext $ctx): string
    {
        return $ctx->sqlNames[$this->path];
    }
}
