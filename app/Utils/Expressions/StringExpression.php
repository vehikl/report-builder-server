<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use App\Utils\Environment;
use Illuminate\Support\Collection;

class StringExpression extends Expression
{
    public function __construct(public readonly string $value)
    {
    }

    public function getDependencies(Entity $entity, Collection $fields): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'type' => 'string',
            'value' => $this->value,
        ];
    }

    public function toSql(Entity $entity, Collection $fields): string
    {
        return "'$this->value'";
    }

    public function evaluate(Environment $environment): string
    {
        return $this->value;
    }
}
