<?php

namespace App\Utils;

use App\Utils\Dependency\DependencyTree;
use App\Utils\Sql\ExtendedAttribute;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QueryMaker
{
    public static function make(DependencyTree $tree, Path $path): Builder
    {
        $columnSelects = array_map(fn (string $column) => "$column as {$path($column)}", $tree->columns);

        $attributeSelects = Arr::map($tree->attributes, function (ExtendedAttribute $attribute, string $attributeName) use ($path) {
            $dependencies = $path->fields($attribute->getSqlDependencies());

            return DB::raw("{$attribute->toSql(...$dependencies)} as {$path($attributeName)}");
        });

        $leftQuery = DB::table($tree->model->getTable())
            ->select($columnSelects);

        if (empty($tree->relations) && empty($attributeSelects)) {
            return $leftQuery;
        }

        $outerQuery = DB::query()->from($leftQuery, '_root_')->select(['*', ...$attributeSelects]);

        foreach ($tree->relations as $relationKey => $dependencyRelation) {
            $relation = $dependencyRelation->relation;

            $newPath = $path->append($relationKey);
            $rightQuery = self::make($dependencyRelation->tree, $newPath);

            $outerQuery->leftJoinSub($rightQuery, $path->relation($relationKey), function (JoinClause $join) use ($path, $relation) {
                $relation->applyLeftJoin($join, ...$path->fields($relation->getLeftJoinDependencies()));
            });
        }

        return $outerQuery;
    }
}
