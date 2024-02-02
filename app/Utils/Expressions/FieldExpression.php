<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use App\Utils\DependencyTracker;
use App\Utils\Environment;
use App\Utils\FieldPath;
use Illuminate\Support\Collection;

class FieldExpression extends Expression
{
    public function __construct(public readonly string $path)
    {
    }

    public function getDbPaths(Entity $entity, Collection $fields): array
    {
        $ModelClass = $entity->getModelClass();

        $paths = (new FieldPath($entity->id, $this->path))->toDbPath($fields);

        return DependencyTracker::getDependencies(new $ModelClass(), $paths);
    }

    public function toArray(): array
    {
        return [
            'type' => 'field',
            'value' => $this->path,
        ];
    }

    public function evaluate(Environment $environment): mixed
    {
        $dbPath = (new FieldPath($environment->entityId, $this->path))->toDbPath($environment->fields);

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
