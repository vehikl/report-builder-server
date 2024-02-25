<?php

namespace App\Utils\Dependency;

use App\Utils\Relations\Joinable;

class DependencyRelation
{
    public readonly DependencyTree $tree;

    public function __construct(public readonly Joinable $relation)
    {
        $this->tree = new DependencyTree($this->relation->getRelated());
    }
}
