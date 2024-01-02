<?php

namespace App\Utils;

use App\Models\Attribute;
use Exception;

class Evaluation
{
    public static function binaryEval($op, $left, $right)
    {
        if (in_array($op, ['=', '<', '>'])) {
            return self::comparisonEval($op, $left, $right);
        }

        if ($left === null || $right === null) {
            return null;
        }

        if (!is_numeric($left) || !is_numeric($right)) {
            throw new Exception('This operation must be done with numbers');
        }

        switch ($op) {
            case '+':
                return $left + $right;
            case '-':
                return $left - $right;
            case '*':
                return $left * $right;
            case '/':
                if ($right === 0) {
                    throw new Exception("Division by zero");
                }
                return $left / $right;
            case '^':
                return $left ** $right;
            default:
                throw new Exception("Invalid operator: $op");
        }
    }

    public static function comparisonEval($op, $left, $right)
    {
        if ($op === '=') {
            return $left === $right;
        }

        if ($left === null || $right === null) {
            return null;
        }

        if ($op === '<') {
            return $left < $right;
        }

        if ($op === '>') {
            return $left > $right;
        }

        throw new Exception("Invalid operator: $op");
    }

    public static function evaluate($node, Environment $env)
    {
        switch ($node['type']) {
            case 'binary':
                $left = self::evaluate($node['left'], $env);
                $right = self::evaluate($node['right'], $env);
                return self::binaryEval($node['op'], $left, $right);

            case 'call':
                $fn = $env->findFunction($node['fn']);
                if (!is_callable($fn)) {
                    throw new Exception("Not a function: {$node['fn']}");
                }
                $args = array_map(fn ($arg) => self::evaluate($arg, $env), $node['args']);
                return $fn(...$args);

            case 'attribute':
                $path = (new AttributePath($env->entityId, $node['value']))->toDbPath(Attribute::query()->get());
                return self::getValueByPath($env->model, $path);

            case 'identifier':
                return $env->findValue($node['value']);

            case 'number':
                return +$node['value'];

            case 'string':
            default:
                return $node['value'];
        }
    }

    private static function getValueByPath(mixed $data, string $path): mixed
    {
        $keys = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } else if (is_object($current) && isset($current->$key)) {
                $current = $current->$key;
            } else {
                return null;
            }
        }

        return $current;
    }

    static public function getPath(string $path, $entityId): string
    {
        $attributes = Attribute::query()->get();
        $identifiers = explode('.', $path);
        $paths = [];
        $currentEntityId = $entityId;
        foreach ($identifiers as $identifier) {
            $attribute = $attributes->where('entity_id', $currentEntityId)->where('identifier', $identifier)->first();
            $paths[] = $attribute->path;

            $currentEntityId = match ($attribute->type->name) {
                'entity', 'collection' => $attribute->type->entityId,
                default => null
            };
        }

        return implode('.', array_filter($paths));
    }
}
