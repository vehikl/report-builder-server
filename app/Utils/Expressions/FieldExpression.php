<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use App\Utils\DependencyTracker;
use App\Utils\FieldPath;
use App\Utils\Path;
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

    public function toSql(Entity $entity, Collection $fields): string
    {
        $ModelClass = $entity->getModelClass();

        return (new Path(new $ModelClass(), null))->field($this->getDbPath($entity->id, $fields));
    }
}
