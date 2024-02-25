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
        Builder $query,
        protected Model $child,
        protected string $listColumn,
        protected int $itemIndex,
        protected string $ownerKey,
        protected string $relationName
    ) {
        parent::__construct($query, $child);
    }

    public function addConstraints(): void
    {
        if (static::$constraints) {
            $table = $this->related->getTable();
            $list = $this->child->{$this->listColumn};

            $this->query->where($table.'.'.$this->ownerKey, '=', $list[$this->itemIndex] ?? null);
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

    public function getResults(): mixed
    {
        $list = $this->child->{$this->listColumn};

        if (is_null($list)) {
            return $this->getDefaultFor($this->parent);
        }

        return $this->query->first() ?: $this->getDefaultFor($this->parent);
    }
}
