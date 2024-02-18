<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use Exception;
use Illuminate\Support\Collection;

class CallExpression extends Expression
{
    /** @var Expression[] */
    public readonly array $args;

    public function __construct(
        public readonly string $identifier,
        Expression ...$args,
    ) {
        $this->args = $args;
    }

    public function getDependencies(Entity $entity, Collection $fields): array
    {
        return array_merge(...array_map(
            fn (Expression $expression) => $expression->getDependencies($entity, $fields),
            $this->args
        ));
    }

    public function toArray(): array
    {
        return [
            'type' => 'call',
            'fn' => $this->identifier,
            'args' => array_map(fn (Expression $expression) => $expression->toArray(), $this->args),
        ];
    }

    public function toSql(Entity $entity, Collection $fields): string
    {
        // TODO: implement
        throw new Exception('Now allowed for now');
    }
}
