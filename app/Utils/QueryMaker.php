<?php

namespace App\Utils;

use App\Models\Core\CoreModel;
use App\Utils\Dependency\DependencyTree;
use App\Utils\Sql\JoinContext;
use App\Utils\Sql\SqlAttribute;
use App\Utils\Sql\SqlContext;
use App\Utils\Sql\SqlName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QueryMaker
{
    public static function make(DependencyTree $tree, PathResolver $resolver): Builder
    {
        $columnSelects = array_map(fn (string $column) => "$column as {$resolver->field($column)}", $tree->columns);

        $attributeSelects = Arr::map($tree->attributes, function (SqlAttribute $attribute, string $attributeName) use ($tree, $resolver) {
            $dependencies = collect($resolver->fields($attribute->getDependencies()))
                ->map(fn (SqlName $path) => self::resolveAttribute($path, $tree->model, $resolver));

            return DB::raw("{$attribute->toSql(new SqlContext(), ...$dependencies)} as {$resolver->field($attributeName)}");
        });

        $leftQuery = DB::table($tree->model->getTable())
            ->select($columnSelects);

        if (empty($tree->relations) && empty($attributeSelects)) {
            return $leftQuery;
        }

        $outerQuery = DB::query()->from($leftQuery, '_root_')->select(['*', ...$attributeSelects]);

        foreach ($tree->relations as $relationKey => $dependencyRelation) {
            $relation = $dependencyRelation->relation;

            $newPath = $resolver->append($relationKey);
            $rightQuery = self::make($dependencyRelation->tree, $newPath);

            $outerQuery->leftJoinSub($rightQuery, $resolver->relation($relationKey), function (JoinClause $join) use ($resolver, $relation) {
                $relation->applyJoin(new JoinContext($join), ...$resolver->fields($relation->getDependencies()));
            });
        }

        return $outerQuery;
    }

    private static function resolveAttribute(SqlName $path, Model $model, PathResolver $resolver): SqlName
    {
        if (self::isLeaf($path) && $attribute = CoreModel::getSqlAttribute($model, $path->__toString())) {
            $dependencies = collect($resolver->fields($attribute->getDependencies()))
                ->map(fn (SqlName $name) => self::resolveAttribute($name, $model, $resolver));

            return SqlName::make("(/* $path */ {$attribute->toSql(new SqlContext(), ...$dependencies)})");
        }

        return $path;
    }

    private static function isLeaf(string $path): bool
    {
        return ! str_contains('__', $path);
    }
}
