<?php

namespace App\Utils\Sql;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Support\Str;

trait HasExtendedRelationships
{
    use HasRelationships;

    public function extendedBelongsTo(string $related, ?string $foreignKey = null, ?string $ownerKey = null, ?string $relation = null): ExtendedBelongsTo
    {
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_'.$instance->getKeyName();
        }

        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new ExtendedBelongsTo($instance->newQuery(), $this, $foreignKey, $ownerKey, $relation);
    }
}
