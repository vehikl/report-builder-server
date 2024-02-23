<?php

namespace App\Utils\Expressions;

use _PHPStan_cc8d35ffb\Nette\Neon\Exception;
use App\Utils\Sql\FunctionDefinition;
use App\Utils\Sql\Sql;
use App\Utils\Sql\SqlName;

class ExpressionContext
{
    /**
     * @param  array<string, string>  $sqlNames
     * @param  array<string, FunctionDefinition>  $functions
     * @param  array<string, mixed>  $variables
     */
    public function __construct(public readonly array $sqlNames, public readonly array $functions, private readonly array $variables)
    {
    }

    /** @param  array<string, string>  $sqlNames */
    public static function make(array $sqlNames): self
    {
        $sql = new Sql();

        $functions = [
            'if' => new FunctionDefinition(fn (SqlName $condition, SqlName $then, SqlName $else): string => $sql->IF($condition, $then, $else)),
            'concat' => new FunctionDefinition(fn (SqlName ...$args): string => $sql->CONCAT(...$args)),
        ];

        $variables = ['current_year' => 2024];

        return new ExpressionContext($sqlNames, $functions, $variables);
    }

    public function getValue(string $name): string
    {
        if (! array_key_exists($name, $this->variables)) {
            throw new Exception("Variable $name does not exist.");
        }

        return (new Sql())->val($this->variables[$name]);
    }
}
