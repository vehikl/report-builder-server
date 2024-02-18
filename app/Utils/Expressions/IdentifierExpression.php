<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use App\Utils\Environment;
use Exception;
use Illuminate\Support\Collection;

class IdentifierExpression extends Expression
{
    public function __construct(public readonly string $identifier)
    {
    }

    public function getDependencies(Entity $entity, Collection $fields): array
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

    public function evaluate(Environment $environment): mixed
    {
        return $environment->findValue($this->identifier);
    }
}
