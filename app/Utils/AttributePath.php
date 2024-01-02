<?php

namespace App\Utils;

use Illuminate\Support\Collection;

class AttributePath
{
    public function __construct(public readonly int $entityId, public readonly string $value)
    {
    }

    // TODO: pass entityId here instead of the constructor
    public function toDbPath(Collection $attributes): string
    {

        $identifiers = explode('.', $this->value);
        $paths = [];
        $currentEntityId = $this->entityId;
        foreach ($identifiers as $identifier) {
            $attribute = $attributes->where('entity_id', $currentEntityId)->where('identifier', $identifier)->first();
            $paths[] = $attribute->path;

            $currentEntityId = match ($attribute->type->name) {
                'entity', 'collection' => $attribute->type->entityId,
                default => null
            };
        }

        return implode('.', array_filter($paths));
    }
}
