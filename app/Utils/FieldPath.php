<?php

namespace App\Utils;

use App\Models\Client\User;
use App\Models\Core\Field;
use Illuminate\Support\Collection;

class FieldPath
{
    public function __construct(public readonly string $entityId, public readonly string $value)
    {
    }

    public function toDataPath(Collection $fields): string
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

    public function canAccess(string $action, User $user, Collection $fields): bool
    {
        $identifiers = explode('.', $this->value);
        $currentEntityId = $this->entityId;

        foreach ($identifiers as $identifier) {
            /** @var Field $field */
            $field = $fields->where('entity_id', $currentEntityId)->where('identifier', $identifier)->first();

            if (! $field->canAccess($action, $user)) {
                return false;
            }

            $currentEntityId = match ($field->type->name) {
                'entity', 'collection' => $field->type->entityId,
                default => null
            };
        }

        return true;
    }
}
