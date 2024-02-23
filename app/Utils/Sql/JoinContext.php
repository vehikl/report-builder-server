<?php

namespace App\Utils\Sql;

use Illuminate\Database\Query\JoinClause;

class JoinContext extends Sql
{
    public function __construct(public readonly JoinClause $join)
    {
    }
}
