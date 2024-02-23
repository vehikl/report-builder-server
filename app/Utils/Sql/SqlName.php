<?php

namespace App\Utils\Sql;

use Stringable;

class SqlName implements Stringable
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
}
