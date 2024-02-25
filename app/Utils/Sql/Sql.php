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

    public function cast(mixed $value, string $type): SqlName
    {
        return SqlName::make("CAST({$this->val($value)} as $type)");
    }

    public function arrayContains(mixed $list, mixed $value): SqlName
    {
        return $this->JSON_CONTAINS($list, $this->cast($value, 'JSON'));
    }

    public function arrayGet(mixed $list, int $index): SqlName
    {
        return $this->JSON_EXTRACT($list, "$[$index]");
    }
}
