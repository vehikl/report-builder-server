<?php

namespace App\Utils\Dependency;

use App\Models\Data\DataModel;
use App\Utils\Sql\ExtendedAttribute;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DependencyTree
{
    /** @var string[] */
    public array $columns = [];

    /** @var array<string, ExtendedAttribute> */
    public array $attributes = [];

    /** @var array<string, DependencyRelation> */
    public array $relations = [];

    public function __construct(public readonly Model $model)
    {
    }

    /** @param string[] $paths */
    public function merge(Model $model, array $paths): static
    {

        if ($this->model::class !== $model::class) {
            throw new Exception('Models are different: '.$this->model::class.' !== '.$model::class);
        }

        $tree = self::make($model, $paths);

        $this->columns = array_unique(array_merge($this->columns, $tree->columns), SORT_REGULAR);
        $this->attributes = array_unique(array_merge($this->attributes, $tree->attributes), SORT_REGULAR);
        $this->relations = array_unique(array_merge($this->relations, $tree->relations), SORT_REGULAR);

        return $this;
    }

    /** @param string[] $paths */
    public static function make(Model $model, array $paths): self
    {
        $tree = new DependencyTree($model);

        foreach ($paths as $path) {
            $keys = explode('.', $path);

            if (empty($keys)) {
                throw new Exception("Path is invalid: $path");
            }

            $currentTree = $tree;

            foreach ($keys as $i => $key) {
                if ($i === array_key_last($keys)) {
                    if (DataModel::isColumn($currentTree->model, $key)) {
                        $currentTree->columns[] = $key;

                        continue;
                    }

                    if ($attribute = DataModel::getSqlAttribute($currentTree->model, $key)) {
                        $currentTree->attributes[$key] = $attribute;

                        $currentTree->merge($currentTree->model, $attribute->getSqlDependencies());

                        continue;
                    }

                    throw new Exception("The key $key in $path is neither a column or an attribute of ".$currentTree->model::class);
                }

                if ($relation = DataModel::getLeftJoinedRelation($currentTree->model, $key)) {
                    if (! array_key_exists($key, $currentTree->relations)) {
                        $currentTree->relations[$key] = new DependencyRelation($relation);
                    }

                    $relationTree = $currentTree->relations[$key]->tree;

                    [$currentDependencies, $relationDependencies] = Collection::make($relation->getLeftJoinDependencies())
                        ->groupBy(fn (string $value) => explode('.', $value)[0] === $key)
                        ->toArray();

                    $currentTree->merge($tree->model, $currentDependencies);

                    $relationTree->merge(
                        $relation->getRelated(),
                        array_map(fn (string $path) => preg_replace('/^\w+\./', '', $path), $relationDependencies)
                    );

                    $currentTree = $relationTree;

                    continue;
                }

                throw new Exception("The key $key in $path is not a relation of ".$currentTree->model::class);
            }
        }

        return $tree;
    }
}
