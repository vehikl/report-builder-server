<?php

namespace App\Utils\Expressions;

use App\Models\Structure\Entity;
use App\Utils\FieldPath;
use App\Utils\Path;
use Illuminate\Support\Collection;

class FieldExpression extends Expression
{
    public function __construct(public readonly string $path)
    {
    }

    /** @return string[] */
    public function getFieldPaths(): array
    {
        return [$this->path];
    }

    public function toArray(): array
    {
        return [
            'type' => 'field',
            'value' => $this->path,
        ];
    }

    public function toSql(array $fieldsSqlNames): string
    {
        return $fieldsSqlNames[$this->path];
    }
}
