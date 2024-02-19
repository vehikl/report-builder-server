<?php

namespace App\Utils\Dependency;

use App\Utils\Sql\ExtendedAttribute;
use Illuminate\Database\Eloquent\Model;

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
}
