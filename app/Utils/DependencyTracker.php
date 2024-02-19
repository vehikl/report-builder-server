<?php

namespace App\Utils;

use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\LeftJoinable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;

class DependencyTracker
{
    /** @return string[] */
    public static function getDependencies(Model $model, string $path): array
    {
        $pathKeys = explode('.', $path);

        if (empty($pathKeys)) {
            throw new Exception("Path is invalid: $path");
        }

        if (count($pathKeys) > 1) {
            return self::getRelationDependencies($model, $pathKeys);
        }

        $currentKey = $pathKeys[0];

        if (self::isColumn($model, $currentKey)) {
            return [$currentKey];
        }

        $attribute = self::getSqlAttribute($model, $currentKey);

        if ($attribute) {
            return array_merge(
                [$currentKey],
                ...array_map(fn (string $path) => self::getDependencies($model, $path), $attribute->getSqlDependencies()),
            );
        }

        throw new Exception("The key $currentKey is neither a column, or an sql attribute in ".$model::class);
    }

    /** @return string[] */
    private static function getRelationDependencies(Model $model, array $pathKeys): array
    {
        $currentKey = $pathKeys[0];

        $relation = self::getLeftJoinedRelation($model, $currentKey);

        if (! $relation) {
            throw new Exception("The key $currentKey is not a relation with a left join definition in ".$model::class);
        }

        $relationDependencies = $relation->getLeftJoinDependencies();

        $originDependencies = array_filter($relationDependencies, fn (string $value) => explode('.', $value)[0] !== $currentKey);
        $relatedDependencies = array_filter($relationDependencies, fn (string $value) => explode('.', $value)[0] === $currentKey);

        $originPaths = array_merge(...array_map(
            fn (string $value) => self::getDependencies($model, $value),
            $originDependencies
        ));

        // TODO: simplify with $paths
        $relatedPaths = array_merge(...array_map(
            function (string $value) use ($relation) {
                $remainingKeys = array_slice(explode('.', $value), 1);

                return self::getDependencies($relation->getRelated(), implode('.', $remainingKeys));
            },
            $relatedDependencies
        ));

        $remainingKeys = array_slice($pathKeys, 1);

        $paths = self::getDependencies($relation->getRelated(), implode('.', $remainingKeys));

        return array_merge(
            array_map(fn (string $path) => "$currentKey.$path", array_merge($paths, $relatedPaths)),
            $originPaths
        );
    }

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
