<?php

namespace App\Models\Structure;

use App\Models\Data\DataModel;
use App\Utils\Environment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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

    public function preview(): array
    {
        $ModelClass = config('models')[$this->entity->getAttribute('table')];

        Log::debug('relations', $this->relations());

        $models = $ModelClass::query()->with($this->relations())->get();

        $fields = Field::query()->get();

        DataModel::disableLazyLoading();
        $records = $this->getRecords($models, $fields);
        DataModel::enableLazyLoading();

        return [
            'name' => $this->name,
            'entity_id' => $this->entity_id,
            'columns' => $this->columns->map(fn (Column $column) => [
                'name' => $column->name,
                'expression' => $column->expression->toArray(),
            ]),
            'records' => $records,
        ];
    }

    public function getRecords(Collection $models, Collection $fields): array
    {
        return $models
            ->map(fn (Model $model) => $this->getRecord(Environment::global($model, $this->entity_id, $fields)))
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
