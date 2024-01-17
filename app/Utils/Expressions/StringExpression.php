<?php

namespace App\Utils\Expressions;

use App\Utils\Environment;
use Illuminate\Support\Collection;

class StringExpression extends Expression
{
    public function __construct(public readonly string $value)
    {
    }

    public function getDbPaths(int $entityId, Collection $attributes): array
    {
        return [];
    }

    public function evaluate(Environment $environment): string
    {
        return $this->value;
    }
}
