<?php

namespace App\Utils\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\SupportsDefaultModels;
use Illuminate\Database\Eloquent\Relations\Relation;

class BelongsToListItem extends Relation implements Joinable
{
    use IsJoinable;
    use SupportsDefaultModels;

    public function __construct(
        Builder          $query,
        protected Model  $child,
        protected string $foreignKeyList,
        protected int    $itemIndex,
        protected string $relatedKey
    ) {
        parent::__construct($query, $child);
    }

    public function addConstraints(): void
    {
        if (static::$constraints) {
            $table = $this->related->getTable();
            $list = $this->child->{$this->foreignKeyList};

            $this->query->where($table.'.'.$this->relatedKey, '=', $list[$this->itemIndex] ?? null);
        }
    }

    public function addEagerConstraints(array $models)
    {
        $relatedKey = $this->related->getTable().'.'.$this->relatedKey;

        $whereIn = $this->whereInMethod($this->related, $this->relatedKey);

        $this->whereInEager($whereIn, $relatedKey, $this->getEagerModelKeys($models));
    }

    protected function getEagerModelKeys(array $models): array
    {
        $foreignKeys = [];

        foreach ($models as $model) {
            $list = $model->{$this->foreignKeyList} ?? [];

            if (! is_null($value = $list[$this->itemIndex] ?? null)) {
                $foreignKeys[] = $value;
            }
        }

        sort($foreignKeys);

        return array_values(array_unique($foreignKeys));
    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->getDefaultFor($model));
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation)
    {
        foreach ($models as $model) {
            $foreignKey = $model->getAttribute($this->foreignKeyList)[$this->itemIndex] ?? null;

            if ($foreignKey !== null && $match = $results->firstWhere($this->relatedKey, $foreignKey)) {
                $model->setRelation($relation, $match);
            }
        }

        return $models;
    }

    protected function newRelatedInstanceFor(Model $parent)
    {
        return $this->related->newInstance();
    }

    public function getResults(): mixed
    {
        $list = $this->child->{$this->foreignKeyList};

        if (is_null($list)) {
            return $this->getDefaultFor($this->parent);
        }

        return $this->query->first() ?: $this->getDefaultFor($this->parent);
    }
}
