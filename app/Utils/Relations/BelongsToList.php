<?php

namespace App\Utils\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class BelongsToList extends Relation
{
    protected bool $respectsListOrder = false;

    public function __construct(Builder $query, protected Model $child, protected string $foreignKeyList, protected string $relatedKey)
    {
        parent::__construct($query, $child);
    }

    public function addConstraints(): void
    {
        if (static::$constraints) {
            $table = $this->related->getTable();
            $list = $this->child->{$this->foreignKeyList};

            $this->query->whereIn($table.'.'.$this->relatedKey, $list);
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
            if (! is_null($values = $model->{$this->foreignKeyList})) {
                $foreignKeys = array_merge($foreignKeys, $values);
            }
        }

        sort($foreignKeys);

        return array_values(array_unique($foreignKeys));
    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation)
    {
        foreach ($models as $model) {
            $foreignKeys = $model->getAttribute($this->foreignKeyList) ?? [];

            $matches = $results->filter(fn ($result) => in_array($result->{$this->relatedKey}, $foreignKeys));

            $model->setRelation($relation, $matches);
        }

        return $models;
    }

    public function getResults(): Collection
    {
        $list = $this->child->{$this->foreignKeyList};

        if (is_null($list)) {
            return $this->related->newCollection();
        }

        $results = $this->get();

        if ($this->respectsListOrder) {
            $results = $results->sortBy(fn (Model $value) => array_search($value->{$this->relatedKey}, $list));
        }

        return $results;
    }

    public function respectingListOrder(bool $value = true): static
    {
        $this->respectsListOrder = $value;

        return $this;
    }
}
