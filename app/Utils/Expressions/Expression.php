<?php

namespace App\Utils\Expressions;

use App\Utils\Environment;
use Exception;
use Illuminate\Support\Collection;

abstract class Expression
{
    abstract public function getDbPaths(int $entityId, Collection $attributes): array;

    abstract public function evaluate(Environment $environment): mixed;

    abstract public function toArray(): array;

    public static function make(array $node): Expression
    {
        return match ($node['type']) {
            'binary' => new BinaryExpression($node['op'], self::make($node['left']), self::make($node['right'])),
            'call' => new CallExpression($node['fn'], ...self::parseMany($node['args'])),
            'attribute' => new AttributeExpression($node['value']),
            'identifier' => new IdentifierExpression($node['value']),
            'number' => new NumberExpression($node['value']),
            'string' => new StringExpression($node['value']),
            default => throw new Exception("Invalid expression type: {$node['type']}"),
        };
    }

    /** @return Expression[] */
    private static function parseMany(array $nodes): array
    {
        return array_map(fn (array $node) => self::make($node), $nodes);
    }
}
