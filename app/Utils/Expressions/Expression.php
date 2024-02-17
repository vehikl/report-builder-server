<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use App\Utils\Environment;
use Exception;
use Illuminate\Support\Collection;

abstract class Expression
{
    abstract public function getDbPaths(Entity $entity, Collection $fields): array;

    abstract public function evaluate(Environment $environment): mixed;

    abstract public function toArray(): array;

    abstract public function toSql(Entity $entity, Collection $fields): string;

    public static function make(array $node): Expression
    {
        return match ($node['type']) {
            'binary' => new BinaryExpression($node['op'], self::make($node['left']), self::make($node['right'])),
            'call' => new CallExpression($node['fn'], ...self::parseMany($node['args'])),
            'field' => new FieldExpression($node['value']),
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
