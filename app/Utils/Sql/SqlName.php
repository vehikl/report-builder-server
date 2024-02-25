<?php

namespace App\Utils\Sql;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use Stringable;

class SqlName implements Expression, Stringable
{
    public function __construct(private readonly string $name)
    {
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /** @return string */
    public function getValue(Grammar $grammar)
    {
        return $this->__toString();
    }
}
