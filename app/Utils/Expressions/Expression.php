<?php

namespace App\Utils\Expressions;

use Exception;

abstract class Expression
{
    /** @return string[] */
    abstract public function getFieldPaths(): array;

    /** @param  array<string, string>  $sqlNames */
    abstract public function toSql(array $sqlNames): string;

    abstract public function toArray(): array;

    public static function make(array $node): Expression
    {
        return match ($node['type']) {
            'binary' => new BinaryExpression($node['op'], self::make($node['left']), self::make($node['right'])),
            'call' => new CallExpression($node['fn'], ...self::makeMany($node['args'])),
            'field' => new FieldExpression($node['value']),
            'identifier' => new IdentifierExpression($node['value']),
            'number' => new NumberExpression($node['value']),
            'string' => new StringExpression($node['value']),
            default => throw new Exception("Invalid expression type: {$node['type']}"),
        };
    }

    /** @return Expression[] */
    private static function makeMany(array $nodes): array
    {
        return array_map(fn (array $node) => self::make($node), $nodes);
    }
}
