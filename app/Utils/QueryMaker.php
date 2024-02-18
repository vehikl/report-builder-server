<?php

namespace App\Utils;

use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\ExtendedBelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;

class QueryMaker
{
    public static function make(Model $model, array $fields, Path $path): Builder
    {
        $tableName = $model->getTable();

        $columns = array_filter($fields, fn (string|array $value) => is_string($value) && in_array($value, Schema::getColumnListing($tableName)));
        $attributes = array_filter($fields, fn (string|array $value) => is_string($value) && ! in_array($value, Schema::getColumnListing($tableName)));
        $relations = array_filter($fields, fn (string|array $value) => is_array($value));

        $columnSelects = array_map(fn (string $column) => "$column as {$path($column)}", $columns);
        $attributeSelects = array_map(function (string $attributeName) use ($model, $path) {
            /** @var ExtendedAttribute $attribute */
            $attribute = self::getSqlAttribute($model, $attributeName);

            $dependencies = array_map(fn (string $value) => $path($value), $attribute->getSqlDependencies());

            return DB::raw("{$attribute->toSql(...$dependencies)} as {$path($attributeName)}");
        }, $attributes);

        $leftQuery = DB::table($tableName)->select($columnSelects);

        if (empty($relations) && empty($attributeSelects)) {
            return $leftQuery;
        }

        $outerQuery = DB::query()->from($leftQuery, '_root_')->select(['*', ...$attributeSelects]);

        foreach ($relations as $relationKey => $fields) {
            /** @var ExtendedBelongsTo $relation */
            $relation = $model->$relationKey();

            $newPath = $path->append($relationKey);
            $rightQuery = self::make($relation->getRelated(), $fields, $newPath);

            $outerQuery->leftJoinSub($rightQuery, $path->relation($relationKey), function (JoinClause $join) use ($path, $relation) {
                $dependencies = array_map(fn (string $dependency) => $path($dependency), $relation->getLeftJoinDependencies());
                $relation->applyLeftJoin($join, ...$dependencies);
            });
        }

        return $outerQuery;
    }

    private static function getSqlAttribute(Model $model, string $name): ?ExtendedAttribute
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
}
