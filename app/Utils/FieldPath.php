<?php

namespace App\Utils;

use Illuminate\Support\Collection;

class FieldPath
{
    public function __construct(public readonly int $entityId, public readonly string $value)
    {
    }

    // TODO: pass entityId here instead of the constructor
    public function toDbPath(Collection $fields): string
    {

        $identifiers = explode('.', $this->value);
        $pathKeys = [];
        $currentEntityId = $this->entityId;
        foreach ($identifiers as $identifier) {
            $field = $fields->where('entity_id', $currentEntityId)->where('identifier', $identifier)->first();
            $pathKeys[] = $field->path;

            $currentEntityId = match ($field->type->name) {
                'entity', 'collection' => $field->type->entityId,
                default => null
            };
        }

        return implode('.', array_filter($pathKeys));
    }
}
