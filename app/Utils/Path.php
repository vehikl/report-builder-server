<?php

namespace App\Utils;

use App\Models\Data\DataModel;
use App\Utils\Sql\ExtendedBelongsTo;
use App\Utils\Sql\SqlName;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Path
{
    public function __construct(private readonly Model $model, private readonly ?string $basePath)
    {
    }

    public function __invoke(string $name): string
    {
        return $this->field($name);
    }

    public function field(string $name): SqlName
    {
        $keys = explode('.', $name);

        $currentModel = $this->model;

        $normalizedName = implode('__', $keys);

        foreach ($keys as $i => $key) {
            if ($i === count($keys) - 1) {
                if (
                    DataModel::isColumn($currentModel, $key) ||
                    DataModel::isSqlAttribute($currentModel, $key)
                ) {
                    $resolvedPath = $this->basePath === null ? $normalizedName : "{$this->basePath}__$normalizedName";

                    return new SqlName($resolvedPath);
                }

                throw new Exception("The key $key is not a column or SQL attribute in ".get_class($currentModel));
            }

            if (!$relation = DataModel::getLeftJoinedRelation($currentModel, $key)) {
                throw new Exception('Not a relation.');
            }

            $currentModel = $relation->getRelated();
        }

        throw new Exception('Something went wrong');
    }

    /**
     * @param  string[]  $names
     * @return SqlName[]
     */
    public function fields(array $names): array
    {
        return array_map(fn (string $name) => $this->field($name), $names);
    }

    public function relation(string $key): string
    {
        if (! DataModel::isLeftJoinedRelation($this->model, $key)) {
            throw new Exception('Not a relation.');
        }

        return $this->basePath === null ? $key : "{$this->basePath}__$key";
    }

    public function append(string $key): static
    {
        if (!$relation = DataModel::getLeftJoinedRelation($this->model, $key)) {
            throw new Exception('Not a relation.');
        }

        $appendedBasePath = $this->basePath === null ? $key : "{$this->basePath}__$key";

        return new Path($relation->getRelated(), $appendedBasePath);
    }
}
