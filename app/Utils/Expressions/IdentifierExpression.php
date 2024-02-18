<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use Exception;
use Illuminate\Support\Collection;

class IdentifierExpression extends Expression
{
    public function __construct(public readonly string $identifier)
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
            'type' => 'identifier',
            'value' => $this->identifier,
        ];
    }

    public function toSql(Entity $entity, Collection $fields): string
    {
        // TODO: implement
        throw new Exception('Now allowed for now');
    }
}
