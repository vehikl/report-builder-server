<?php

namespace App\Utils\Expressions;

use App\Utils\AttributePath;
use App\Utils\Environment;
use Illuminate\Support\Collection;

class AttributeExpression extends Expression
{
    public function __construct(public readonly string $path)
    {
    }

    public function getDbPaths(int $entityId, Collection $attributes): array
    {
        return [(new AttributePath($entityId, $this->path))->toDbPath($attributes)];
    }

    public function evaluate(Environment $environment): mixed
    {
        $dbPath = (new AttributePath($environment->entityId, $this->path))->toDbPath($environment->attributes);

        return self::getValueByPath($environment->model, $dbPath);
    }

    private static function getValueByPath(mixed $data, string $path): mixed
    {
        $keys = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } elseif (is_object($current) && isset($current->$key)) {
                $current = $current->$key;
            } else {
                return null;
            }
        }

        return $current;
    }
}
