<?php

namespace App\Utils\Expressions;

use App\Utils\Sql\FunctionDefinition;
use App\Utils\Sql\Sql;
use App\Utils\Sql\SqlName;

class ExpressionContext
{
    /**
     * @param  array<string, string>  $sqlNames
     * @param  array<string, FunctionDefinition>  $functions
     */
    public function __construct(public readonly array $sqlNames, public readonly array $functions)
    {
    }

    /** @param  array<string, string>  $sqlNames */
    public static function make(array $sqlNames): self
    {
        $fn = new Sql();

        $functions = [
            'if' => new FunctionDefinition(fn (SqlName $condition, SqlName $then, SqlName $else): string => $fn->IF($condition, $then, $else)),
            'concat' => new FunctionDefinition(fn (SqlName ...$args): string => $fn->CONCAT(...$args)),
        ];

        return new ExpressionContext($sqlNames, $functions);
    }
}
