<?php

namespace App\Utils\Expressions;

class FieldExpression extends Expression
{
    public function __construct(public readonly string $path)
    {
    }

    /** @return string[] */
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

    public function toSql(array $sqlNames): string
    {
        return $sqlNames[$this->path];
    }
}
