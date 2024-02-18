<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use Exception;
use Illuminate\Support\Collection;

class BinaryExpression extends Expression
{
    public function __construct(
        public readonly string $operator,
        public readonly Expression $left,
        public readonly Expression $right,
    ) {
        if (! in_array($this->operator, ['=', '<', '>', '+', '-', '*', '/', '^'])) {
            throw new Exception("Invalid operator $this->operator");
        }
    }

    public function getDependencies(Entity $entity, Collection $fields): array
    {
        return [
            ...$this->left->getDependencies($entity, $fields),
            ...$this->right->getDependencies($entity, $fields),
        ];
    }

    public function toArray(): array
    {
        return [
            'type' => 'binary',
            'op' => $this->operator,
            'left' => $this->left->toArray(),
            'right' => $this->right->toArray(),
        ];
    }

    public function toSql(Entity $entity, Collection $fields): string
    {
        return "{$this->left->toSql($entity, $fields)} $this->operator {$this->right->toSql($entity, $fields)}";
    }
}
