<?php

namespace App\Utils;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Environment
{
    public static function global(Model $model, int $entityId): Environment
    {
        return new Environment($model, $entityId, [], [
            'if' => fn($condition, $then, $otherwise) => $condition ? $then : $otherwise
        ]);
    }

    public function __construct(
        public readonly Model             $model,
        public readonly int               $entityId,
        private readonly array            $values = [],
        private readonly array            $functions = [],
        private readonly Environment|null $parent = null
    )
    {
    }

    public function findValue($identifier)
    {
        if (array_key_exists($identifier, $this->values)) {
            return $this->values[$identifier];
        }

        if ($this->parent !== null) {
            return $this->parent->findValue($identifier);
        }

        throw new Exception("Unknown value identifier \"$identifier\"");
    }

    public function findFunction($identifier)
    {
        if (array_key_exists($identifier, $this->functions)) {
            return $this->functions[$identifier];
        }

        if ($this->parent !== null) {
            return $this->parent->findFunction($identifier);
        }

        throw new Exception("Unknown function identifier \"$identifier\"");
    }
}
