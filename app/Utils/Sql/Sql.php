<?php

namespace App\Utils\Sql;

use BadMethodCallException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Sql
{
    public static function make(): self
    {
        return new Sql();
    }

    public function val(mixed $value): string
    {
        return is_a($value, SqlName::class) ? $value : DB::escape($value);
    }

    public function __call(string $name, array $arguments): SqlName
    {
        if (Str::upper($name) !== $name) {
            throw new BadMethodCallException('Call to undefined method '.static::class.'::'."$name().");
        }

        $escaped = array_map(
            fn (mixed $value) => $this->val($value),
            $arguments
        );

        $args = implode(', ', $escaped);

        return SqlName::make("$name($args)");
    }

    public function not(mixed $value): SqlName
    {
        return SqlName::make("NOT {$this->val($value)}");
    }

    public function notNull(mixed $value): SqlName
    {
        return SqlName::make("NOT {$this->ISNULL($value)}");
    }

    public function cast(mixed $value, string $type): SqlName
    {
        return SqlName::make("CAST({$this->val($value)} as $type)");
    }

    public function true(): SqlName
    {
        return SqlName::make('TRUE');
    }

    public function false(): SqlName
    {
        return SqlName::make('FALSE');
    }

    public function arrayContains(mixed $array, mixed $value): SqlName
    {
        $arrayArg = is_array($array) ? implode(',', $array) : $array;

        return $this->arrayContains($arrayArg, $value);
    }

    public function jsonArrayContains(mixed $array, mixed $value): SqlName
    {
        return $this->JSON_CONTAINS($array, $this->cast($value, 'JSON'));
    }

    public function jsonArrayGet(mixed $array, int $index): SqlName
    {
        return $this->JSON_EXTRACT($array, "$[$index]");
    }
}
