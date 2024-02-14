<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use App\Utils\DependencyTracker;
use App\Utils\Environment;
use App\Utils\FieldPath;
use Illuminate\Support\Collection;

class FieldExpression extends Expression
{
    private ?string $dbPath = null;

    private function getDbPath(int $entityId, Collection $fields): string
    {
        if ($this->dbPath === null) {
            $this->dbPath = (new FieldPath($entityId, $this->path))->toDbPath($fields);
        }

        return $this->dbPath;
    }

    public function __construct(public readonly string $path)
    {
    }

    public function getDbPaths(Entity $entity, Collection $fields): array
    {
        $ModelClass = $entity->getModelClass();

        return DependencyTracker::getDependencies(new $ModelClass(), $this->getDbPath($entity->id, $fields));
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
        return self::getValueByPath($environment->model, $this->getDbPath($environment->entityId, $environment->fields));
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
