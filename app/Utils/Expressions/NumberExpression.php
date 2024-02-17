<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use App\Utils\Environment;
use Exception;
use Illuminate\Support\Collection;

class NumberExpression extends Expression
{
    public function __construct(public readonly string $value)
    {
        if (! is_numeric($this->value)) {
            throw new Exception("Not a number: $this->value");
        }
    }

    public function getDbPaths(Entity $entity, Collection $fields): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'type' => 'number',
            'value' => $this->value,
        ];
    }

    public function toSql(Entity $entity, Collection $fields): string
    {
        return $this->value;
    }

    public function evaluate(Environment $environment): float
    {
        return floatval($this->value);
    }
}
