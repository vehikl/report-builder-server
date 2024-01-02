<?php

namespace App\Models;

use App\Utils\Environment;
use App\Utils\Evaluation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class);
    }

    public function relations(): array
    {
        return $this->columns
            ->flatMap(fn(Column $column) => $column->relations())
            ->filter()
            ->toArray();
    }

    public function preview(Collection $models): array
    {
        return [
            'name' => $this->name,
            'entity_id' => $this->entity_id,
            'columns' => $this->columns->map(fn(Column $column) => [
                'name' => $column->name,
                'expression' => $column->expression
            ]),
            'records' => $this->getRecords($models),
        ];
    }

    public function getRecords(Collection $models): array
    {
        return $models
            ->map(fn(Model $model) => $this->getRecord($model))
            ->toArray();
    }

    public function getRecord(Model $model): array
    {
        $env = Environment::global($model, $this->entity_id);

        return $this->columns
            ->mapWithKeys(fn(Column $column) => [
                $column->name => Evaluation::evaluate($column->ast(), $env)
            ])
            ->toArray();
    }
}
