<?php

namespace App\Utils\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class BelongsToList extends Relation
{
    protected bool $respectsListOrder = false;

    public function __construct(Builder $query, protected Model $child, protected string $listColumn, protected string $ownerKey, protected string $relationName)
    {
        parent::__construct($query, $child);
    }

    public function addConstraints(): void
    {
        if (static::$constraints) {
            $table = $this->related->getTable();
            $list = $this->child->{$this->listColumn};

            $this->query->whereIn($table.'.'.$this->ownerKey, $list);
        }
    }

    public function addEagerConstraints(array $models)
    {
        // TODO: Implement addEagerConstraints() method.
    }

    public function initRelation(array $models, $relation)
    {
        // TODO: Implement initRelation() method.
    }

    public function match(array $models, Collection $results, $relation)
    {
        // TODO: Implement match() method.
    }

    protected function newRelatedInstanceFor(Model $parent)
    {
        // TODO: Implement newRelatedInstanceFor() method.
    }

    public function getResults(): Collection
    {
        $list = $this->child->{$this->listColumn};

        if (is_null($list)) {
            return $this->related->newCollection();
        }

        if (! is_array($list)) {
            $list = json_decode($list);
        }

        $results = $this->get();

        if ($this->respectsListOrder) {
            $results = $results->sortBy(fn (Model $value) => array_search($value->{$this->ownerKey}, $list));
        }

        return $results;
    }

    public function respectingListOrder(bool $value = true): static
    {
        $this->respectsListOrder = $value;

        return $this;
    }
}
