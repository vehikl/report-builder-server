<?php

namespace App\Utils;

use App\Models\Core\CoreModel;
use App\Utils\Sql\SqlName;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Path
{
    public function __construct(private readonly Model $model, private readonly ?string $basePath)
    {
    }

    public function field(string $name): SqlName
    {
        $keys = explode('.', $name);

        $currentModel = $this->model;

        $normalizedName = implode('__', $keys);

        foreach ($keys as $i => $key) {
            if ($i === count($keys) - 1) {
                if (
                    CoreModel::isColumn($currentModel, $key) ||
                    CoreModel::isSqlAttribute($currentModel, $key)
                ) {
                    $resolvedPath = $this->basePath === null ? $normalizedName : "{$this->basePath}__$normalizedName";

                    return SqlName::make($resolvedPath);
                }

                throw new Exception("The key $key is not a column or SQL attribute in ".get_class($currentModel));
            }

            if (! $relation = CoreModel::getJoinedRelation($currentModel, $key)) {
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
        if (! CoreModel::isJoinedRelation($this->model, $key)) {
            throw new Exception('Not a relation.');
        }

        return $this->basePath === null ? $key : "{$this->basePath}__$key";
    }

    public function append(string $key): static
    {
        if (! $relation = CoreModel::getJoinedRelation($this->model, $key)) {
            throw new Exception('Not a relation.');
        }

        $appendedBasePath = $this->basePath === null ? $key : "{$this->basePath}__$key";

        return new Path($relation->getRelated(), $appendedBasePath);
    }
}
