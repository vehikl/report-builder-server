<?php

namespace App\Utils\Expressions;

use App\Utils\Environment;
use Illuminate\Support\Collection;

class IdentifierExpression extends Expression
{
    public function __construct(public readonly string $identifier)
    {
    }

    public function getDbPaths(int $entityId, Collection $fields): array
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

    public function evaluate(Environment $environment): mixed
    {
        return $environment->findValue($this->identifier);
    }
}
