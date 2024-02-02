<?php

namespace App\Utils;

use App\Utils\PhpAttributes\Dependencies;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

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

        $method = self::getMethod($model, $currentKey);

        if ($method) {
            return self::getMethodDependencies($model, $method);
        }

        throw new Exception("The key $currentKey is neither a column, a method, or a mutator in ".$model::class);
    }

    /** @return string[] */
    private static function getMethodDependencies(Model $model, ReflectionMethod $method): array
    {
        $attribute = ($method->getAttributes(Dependencies::class)[0] ?? null)?->newInstance();

        if (! $attribute) {
            throw new Exception("The method $method->name in ".$model::class.' does not have any dependencies.');
        }

        return array_merge(
            ...array_map(fn (string $path) => self::getDependencies($model, $path), $attribute->paths)
        );
    }

    /** @return string[] */
    private static function getRelationDependencies(Model $model, array $pathKeys): array
    {
        $currentKey = $pathKeys[0];

        if (! $model->isRelation($currentKey)) {
            throw new Exception("The key $currentKey is not a relation in ".$model::class);
        }

        /** @var Relation $relation */
        $relation = $model->$currentKey();

        $remainingKeys = array_slice($pathKeys, 1);

        $paths = self::getDependencies($relation->getRelated(), implode('.', $remainingKeys));

        return array_map(fn (string $path) => "$currentKey.$path", $paths);
    }

    private static function isColumn(Model $model, string $key): bool
    {
        return in_array($key, Schema::getColumnListing($model->getTable()));
    }

    private static function getMethod(Model $model, string $key): ?ReflectionMethod
    {
        $methodName = $model->hasAttributeMutator($key) ? Str::camel($key) : $key;

        return collect((new ReflectionClass($model))->getMethods())
            ->first(fn (ReflectionMethod $method) => $method->getName() === $methodName);
    }
}
