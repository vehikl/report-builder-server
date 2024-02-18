<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use Illuminate\Support\Collection;

class StringExpression extends Expression
{
    public function __construct(public readonly string $value)
    {
    }

    /** @return string[] */
    public function getFieldPaths(): array
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
}
