<?php

namespace App\Utils;

use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\ExtendedBelongsTo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;

class Path
{
    public function __construct(private readonly Model $model, private readonly ?string $basePath)
    {
    }

    public function __invoke(string $name): string
    {
        return $this->field($name);
    }

    public function field(string $name): string
    {
        $keys = explode('.', $name);

        $currentModel = $this->model;

        $normalizedName = implode('__', $keys);

        foreach ($keys as $i => $key) {
            if ($i === count($keys) - 1) {
                $columns = Schema::getColumnListing($currentModel->getTable());

                if (in_array($key, $columns)) {

                    return $this->basePath === null ? $normalizedName : "{$this->basePath}__$normalizedName";
                }

                $sqlAttribute = $this->getSqlAttribute($currentModel, $key);

                if ($sqlAttribute) {
                    return $this->basePath === null ? $normalizedName : "{$this->basePath}__$normalizedName";
                }

                throw new Exception("The key $key is not a column or SQL attribute in ".get_class($currentModel));
            }

            if (! $this->model->isRelation($key)) {
                throw new Exception('Not a relation.');
            }

            /** @var Relation $relation */
            $relation = $currentModel->$key();

            $currentModel = $relation->getRelated();
        }

        throw new Exception('Something went wrong');
    }

    public function relation(string $key): string
    {
        if (! $this->model->isRelation($key)) {
            throw new Exception('Not a relation.');
        }

        return $this->basePath === null ? $key : "{$this->basePath}__$key";
    }

    public function append(string $key): static
    {
        // TODO: if is relation and Extended... and hasLeftJoinDefinition
        /** @var ExtendedBelongsTo $relation */
        $relation = $this->model->$key();

        $appendedBasePath = $this->basePath === null ? $key : "{$this->basePath}__$key";

        return new Path($relation->getRelated(), $appendedBasePath);
    }

    private function getSqlAttribute(Model $model, string $name): ?ExtendedAttribute
    {
        if (! $model->hasAttributeMutator($name)) {
            return null;
        }

        $attribute = (new ReflectionClass($model))->getMethod(Str::camel($name))->invoke($model);

        if (! is_a($attribute, ExtendedAttribute::class) || ! $attribute->hasSqlDefinition()) {
            return null;
        }

        return $attribute;
    }
}
