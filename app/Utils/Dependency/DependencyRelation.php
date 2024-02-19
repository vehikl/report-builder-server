<?php

namespace App\Utils\Dependency;

use App\Utils\Sql\LeftJoinable;

class DependencyRelation
{
    public readonly DependencyTree $tree;

    public function __construct(public readonly LeftJoinable $relation)
    {
        $this->tree = new DependencyTree($this->relation->getRelated());
    }
}
