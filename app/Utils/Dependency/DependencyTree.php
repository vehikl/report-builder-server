<?php

namespace App\Utils\Dependency;

use App\Models\Core\CoreModel;
use App\Utils\Sql\SqlAttribute;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DependencyTree
{
    /** @var string[] */
    public array $columns = [];

    /** @var array<string, SqlAttribute> */
    public array $attributes = [];

    /** @var array<string, DependencyRelation> */
    public array $relations = [];

    public function __construct(public readonly Model $model)
    {
    }

    public function merge(DependencyTree $tree): self
    {
        if ($this->model::class !== $tree->model::class) {
            throw new Exception('Models are different: '.$this->model::class.' !== '.$tree->model::class);
        }

        $this->columns = array_unique(array_merge($this->columns, $tree->columns), SORT_REGULAR);
        $this->attributes = array_unique(array_merge($this->attributes, $tree->attributes), SORT_REGULAR);

        /** @var string[] $keys */
        $keys = array_unique([...array_keys($this->relations), ...array_keys($tree->relations)]);

        foreach ($keys as $key) {
            $dependencyRelation = $this->relations[$key] ?? $tree->relations[$key];

            if (isset($this->relations[$key]) && isset($tree->relations[$key])) {
                $dependencyRelation->tree->merge($tree->relations[$key]->tree);
            }

            $this->relations[$key] = $dependencyRelation;
        }

        return $this;
    }

    /** @param  string[]  $paths */
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
                    if (CoreModel::isColumn($currentTree->model, $key)) {
                        if (! in_array($key, $currentTree->columns)) {
                            $currentTree->columns[] = $key;
                        }

                        continue;
                    }

                    if ($attribute = CoreModel::getSqlAttribute($currentTree->model, $key)) {
                        $currentTree->attributes[$key] = $attribute;

                        $currentTree->merge(self::make($currentTree->model, $attribute->getDependencies()));

                        continue;
                    }

                    throw new Exception("The key $key in $path is neither a column or an attribute of ".$currentTree->model::class);
                }

                if ($relation = CoreModel::getJoinedRelation($currentTree->model, $key)) {
                    if (! array_key_exists($key, $currentTree->relations)) {
                        $currentTree->relations[$key] = new DependencyRelation($relation);
                    }

                    $relationTree = $currentTree->relations[$key]->tree;

                    [$currentDependencies, $relationDependencies] = Collection::make($relation->getDependencies())
                        ->groupBy(fn (string $value) => explode('.', $value)[0] === $key)
                        ->toArray();

                    $currentTree->merge(self::make($tree->model, $currentDependencies));

                    $relationTree->merge(self::make(
                        $relation->getRelated(),
                        array_map(fn (string $path) => preg_replace('/^\w+\./', '', $path), $relationDependencies)
                    ));

                    $currentTree = $relationTree;

                    continue;
                }

                throw new Exception("The key $key in $path is not a relation of ".$currentTree->model::class);
            }
        }

        return $tree;
    }
}
