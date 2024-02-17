<?php

namespace App\Utils;

use App\Utils\Sql\ExtendedBelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class QueryMaker
{
    public static function make(Model $model, array $fields, Path $path): Builder
    {
        $tableName = $model->getTable();

        $columns = array_filter($fields, fn (string|array $value) => is_string($value));
        $relations = array_filter($fields, fn (string|array $value) => is_array($value));

        $selects = array_map(fn (string $column) => "$column as {$path($column)}", $columns);

        $leftQuery = DB::table($tableName)->select($selects);

        if (empty($relations)) {
            return $leftQuery;
        }

        $outerQuery = DB::query()->from($leftQuery, $tableName);

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
}
