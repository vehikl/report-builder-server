<?php

namespace App\Utils\Expressions;

use App\Utils\Environment;
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

    public function getDbPaths(int $entityId, Collection $attributes): array
    {
        return [
            ...$this->left->getDbPaths($entityId, $attributes),
            ...$this->right->getDbPaths($entityId, $attributes),
        ];
    }

    public function evaluate(Environment $environment): mixed
    {

        if (in_array($this->operator, ['=', '<', '>'])) {
            return $this->evaluateComparison($environment);
        }

        $left = $this->left->evaluate($environment);
        $right = $this->right->evaluate($environment);

        if ($left === null || $right === null) {
            return null;
        }

        if (! is_numeric($left) || ! is_numeric($right)) {
            throw new Exception('This operation must be done with numbers');
        }

        switch ($this->operator) {
            case '+':
                return $left + $right;
            case '-':
                return $left - $right;
            case '*':
                return $left * $right;
            case '/':
                if ($right === 0) {
                    throw new Exception('Division by zero');
                }

                return $left / $right;
            case '^':
                return $left ** $right;
            default:
                throw new Exception("Invalid operator: $this->operator");
        }
    }

    private function evaluateComparison(Environment $environment): ?bool
    {
        $left = $this->left->evaluate($environment);
        $right = $this->right->evaluate($environment);

        if ($this->operator === '=') {
            return $left === $right;
        }

        if ($left === null || $right === null) {
            return null;
        }

        if ($this->operator === '<') {
            return $left < $right;
        }

        if ($this->operator === '>') {
            return $left > $right;
        }

        throw new Exception("Invalid operator: $this->operator");
    }
}
