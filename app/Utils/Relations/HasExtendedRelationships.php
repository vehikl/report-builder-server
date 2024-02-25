<?php

namespace App\Utils\Relations;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Support\Str;

trait HasExtendedRelationships
{
    use HasRelationships;

    public function joinableBelongsTo(string $related, ?string $foreignKey = null, ?string $ownerKey = null, ?string $relation = null): JoinableBelongsTo
    {
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_'.$instance->getKeyName();
        }

        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new JoinableBelongsTo($instance->newQuery(), $this, $foreignKey, $ownerKey, $relation);
    }

    public function belongsToList(string $related, string $listColumn, ?string $ownerKey = null, ?string $relationName = null): BelongsToList
    {
        if (is_null($relationName)) {
            $relationName = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new BelongsToList($instance->newQuery(), $this, $listColumn, $ownerKey, $relationName);
    }

    public function belongsToListItem(string $related, string $listColumn, int $itemIndex, ?string $ownerKey = null, ?string $relationName = null): BelongsToListItem
    {
        if (is_null($relationName)) {
            $relationName = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new BelongsToListItem($instance->newQuery(), $this, $listColumn, $itemIndex, $ownerKey, $relationName);
    }
}
