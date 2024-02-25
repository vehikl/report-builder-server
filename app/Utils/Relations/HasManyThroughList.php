<?php

namespace App\Utils\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class HasManyThroughList extends Relation
{
    public function __construct(Builder $query, Model $parent, protected string $foreignList, protected string $localKey)
    {
        parent::__construct($query, $parent);
    }

    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->whereJsonContains($this->foreignList, $this->getParentKey());
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

    public function getParentKey()
    {
        return $this->parent->getAttribute($this->localKey);
    }

    public function getResults()
    {
        return is_null($this->getParentKey())
            ? $this->related->newCollection()
            : $this->query->get();
    }
}
