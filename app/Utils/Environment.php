<?php

namespace App\Utils;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Environment
{
    public static function global(Model $model, int $entityId, Collection $fields): Environment
    {
        $values = ['current_year' => (new Carbon())->year];
        $functions = ['if' => fn ($condition, $then, $otherwise) => $condition ? $then : $otherwise];

        return new Environment($model, $entityId, $fields, $values, $functions);
    }

    public function __construct(
        public readonly Model $model,
        public readonly int $entityId,
        public readonly Collection $fields,
        private readonly array $values = [],
        private readonly array $functions = [],
        private readonly ?Environment $parent = null
    ) {
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
