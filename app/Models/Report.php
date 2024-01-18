<?php

namespace App\Models;

use App\Utils\Environment;
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
            ->flatMap(fn (Column $column) => $column->relations())
            ->filter()
            ->toArray();
    }

    public function preview(Collection $models): array
    {
        $attributes = Attribute::query()->get();

        return [
            'name' => $this->name,
            'entity_id' => $this->entity_id,
            'columns' => $this->columns->map(fn (Column $column) => [
                'name' => $column->name,
                'expression' => $column->expression->toArray(),
            ]),
            'records' => $this->getRecords($models, $attributes),
        ];
    }

    public function getRecords(Collection $models, Collection $attributes): array
    {
        return $models
            ->map(fn (Model $model) => $this->getRecord(Environment::global($model, $this->entity_id, $attributes)))
            ->toArray();
    }

    public function getRecord(Environment $environment): array
    {
        return $this->columns
            ->mapWithKeys(fn (Column $column) => [
                $column->name => $column->expression->evaluate($environment),
            ])
            ->toArray();
    }
}
