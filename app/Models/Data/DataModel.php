<?php

namespace App\Models\Data;

use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\HasExtendedRelationships;
use App\Utils\Sql\LeftJoinable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;

class DataModel extends Model
{
    use HasExtendedRelationships;

    public static function isColumn(Model $model, string $key): bool
    {
        return in_array($key, Schema::getColumnListing($model->getTable()));
    }

    public static function getSqlAttribute(Model $model, $name): ?ExtendedAttribute
    {
        if (! $model->hasAttributeMutator($name)) {
            return null;
        }

        $attribute = (new ReflectionClass($model))->getMethod(Str::camel($name))->invoke($model);

        if (! is_a($attribute, ExtendedAttribute::class) || ! $attribute->hasSqlDefinition()) {
            return null;
        }

        return $attribute;
    }

    public static function isSqlAttribute(Model $model, string $name): bool
    {
        return boolval(self::getSqlAttribute($model, $name));
    }

    public static function getLeftJoinedRelation(Model $model, $key): ?LeftJoinable
    {
        if (! $model->isRelation($key)) {
            return null;
        }

        /** @var Relation $relation */
        $relation = $model->$key();

        if (! is_a($relation, LeftJoinable::class) || ! $relation->hasLeftJoinDefinition()) {
            return null;
        }

        return $relation;
    }
}
