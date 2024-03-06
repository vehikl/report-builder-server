<?php

namespace App\Utils;

use App\Models\Core\CoreModel;
use App\Utils\Dependency\DependencyTree;
use App\Utils\Sql\JoinContext;
use App\Utils\Sql\SqlAttribute;
use App\Utils\Sql\SqlContext;
use App\Utils\Sql\SqlName;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QueryMaker
{
    public static function make(DependencyTree $tree, Path $path): Builder
    {
        $columnSelects = array_map(fn (string $column) => "$column as {$path->field($column)}", $tree->columns);

        $attributeSelects = Arr::map($tree->attributes, function (SqlAttribute $attribute, string $attributeName) use ($tree, $path) {
            $dependencies = collect($path->fields($attribute->getDependencies()))
                // TODO: extract this logic
                ->map(fn (SqlName $value) => str_contains('__', $value) || ! ($depAttribute = CoreModel::getSqlAttribute($tree->model, $value->__toString())) ? $value : SqlName::make("({$depAttribute->toSql(new SqlContext(), ...$path->fields($depAttribute->getDependencies()))})"));

            return DB::raw("{$attribute->toSql(new SqlContext(), ...$dependencies)} as {$path->field($attributeName)}");
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
                $relation->applyJoin(new JoinContext($join), ...$path->fields($relation->getDependencies()));
            });
        }

        return $outerQuery;
    }
}
