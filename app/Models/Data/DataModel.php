<?php

namespace App\Models\Data;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;

class DataModel extends Model
{
    private static bool $isLazyLoadingDisabled = false;

    protected function getRelationshipFromMethod($method)
    {
        if (self::$isLazyLoadingDisabled) {
            $class = get_class($this);
            throw new BadMethodCallException("Attempted to lazy load [$method] on model [$class] but lazy loading is disabled.");
        }

        return parent::getRelationshipFromMethod($method);
    }

    public static function disableLazyLoading(): void
    {
        self::$isLazyLoadingDisabled = true;
    }

    public static function enableLazyLoading(): void
    {
        self::$isLazyLoadingDisabled = false;
    }
}
