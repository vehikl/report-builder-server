<?php

namespace App\Models\Core;

use App\Utils\Relations\HasExtendedRelationships;
use App\Utils\Relations\Joinable;
use App\Utils\Sql\SqlAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;

class CoreModel extends Model
{
    use HasExtendedRelationships;

    private static array $tableColumns = [];

    public static function isColumn(Model $model, string $key): bool
    {
        $table = $model->getTable();

        if (! isset(self::$tableColumns[$table])) {
            self::$tableColumns[$table] = Schema::getColumnListing($table);
        }

        return in_array($key, self::$tableColumns[$table]);
    }

    public static function getSqlAttribute(Model $model, string $name): ?SqlAttribute
    {
        if (! $model->hasAttributeMutator($name)) {
            return null;
        }

        $attribute = (new ReflectionClass($model))->getMethod(Str::camel($name))->invoke($model);

        if (! is_a($attribute, SqlAttribute::class)) {
            return null;
        }

        return $attribute;
    }

    public static function isSqlAttribute(Model $model, string $name): bool
    {
        return boolval(self::getSqlAttribute($model, $name));
    }

    public static function getJoinedRelation(Model $model, $key): ?Joinable
    {
        if (! $model->isRelation($key)) {
            return null;
        }

        /** @var Relation $relation */
        $relation = $model->$key();

        if (! is_a($relation, Joinable::class) || ! $relation->hasJoin()) {
            return null;
        }

        return $relation;
    }

    public static function isJoinedRelation(Model $model, $name): bool
    {
        return boolval(self::getJoinedRelation($model, $name));
    }
}
