<?php

namespace App\Utils;

use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\ExtendedBelongsTo;
use App\Utils\Sql\SqlName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QueryMaker
{
    public static function make(Model $model, array $fields, Path $path): Builder
    {
        $tableName = $model->getTable();

        $relations = $fields['relations'];

        $columnSelects = array_map(fn (string $column) => "$column as {$path($column)}", $fields['columns']);

        $attributeSelects = Arr::map($fields['attributes'], function (ExtendedAttribute $attribute, string $attributeName) use ($model, $path) {
            $dependencies = array_map(fn (string $value) => new SqlName($path($value)), $attribute->getSqlDependencies());
            return DB::raw("{$attribute->toSql(...$dependencies)} as {$path($attributeName)}");
        });

        $leftQuery = DB::table($tableName)->select($columnSelects);

        if (empty($relations) && empty($attributeSelects)) {
            return $leftQuery;
        }

        $outerQuery = DB::query()->from($leftQuery, '_root_')->select(['*', ...$attributeSelects]);

        foreach ($relations as $relationKey => $fields) {
            /** @var ExtendedBelongsTo $relation */
            $relation = $fields['relation'];

            $newPath = $path->append($relationKey);
            $rightQuery = self::make($relation->getRelated(), $fields, $newPath);

            $outerQuery->leftJoinSub($rightQuery, $path->relation($relationKey), function (JoinClause $join) use ($path, $relation) {
                $dependencies = array_map(fn (string $dependency) => new SqlName($path($dependency)), $relation->getLeftJoinDependencies());
                $relation->applyLeftJoin($join, ...$dependencies);
            });
        }

        return $outerQuery;
    }
}
