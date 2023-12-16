<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class);
    }

    public function relations(): array
    {
        return $this->columns
            ->map(fn(Column $column) => $column->relation())
            ->filter()
            ->toArray();
    }

    public function getRecord(Model $model): array
    {
        return $this->columns
            ->mapWithKeys(fn(Column $column) => [
                $column->name => self::getValueByPath($model, $column->expression)
            ])
            ->toArray();
    }

    public function getRecords(Collection $models): array
    {
        return $models
            ->map(fn(Model $model) => $this->getRecord($model))
            ->toArray();
    }

    public function preview(Collection $models): array
    {
        return [
            'name' => $this->name,
            'headers' => $this->columns->pluck('name'),
            'records' => $this->getRecords($models),
        ];
    }

    private static function getValueByPath(mixed $data, string $path): mixed
    {
        $keys = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } else if (is_object($current) && isset($current->$key)) {
                $current = $current->$key;
            } else {
                return null;
            }
        }

        return $current;
    }
}
