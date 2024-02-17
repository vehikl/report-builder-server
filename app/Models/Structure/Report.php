<?php

namespace App\Models\Structure;

use App\Utils\Environment;
use App\Utils\Path;
use App\Utils\QueryMaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    public function dependencies(): array
    {
        return $this->columns
            ->flatMap(fn (Column $column) => $column->dependencies())
            ->unique()
            ->toArray();
    }

    public function relations(): array
    {
        return $this->columns
            ->flatMap(fn (Column $column) => $column->relations())
            ->filter()
            ->toArray();
    }

    public function getQueryStructure(): array
    {
        $structure = [];

        foreach ($this->dependencies() as $path) {
            $keys = explode('.', $path);
            $substructure = &$structure;

            foreach ($keys as $i => $key) {
                if ($i === array_key_last($keys)) {
                    $substructure[] = $key;

                    continue;
                }

                if (! isset($substructure[$key])) {
                    $substructure[$key] = [];
                }
                $substructure = &$substructure[$key];
            }
        }

        return $structure;

    }

    public function getQuery(): Builder
    {
        $model = new ($this->entity->getModelClass());

        $query = QueryMaker::make($model, $this->getQueryStructure(), new Path($model, null));

        $selects = $this->columns
            ->map(fn (Column $column) => DB::raw($column->getSelect()))
            ->toArray();

        return $query->select($selects);
    }

    public function preview(): array
    {
        return [
            'name' => $this->name,
            'entity_id' => $this->entity_id,
            'columns' => $this->columns->map(fn (Column $column) => [
                'name' => $column->name,
                'key' => $column->key,
                'expression' => $column->expression->toArray(),
            ]),
            'records' => $this->getQuery()->get(),
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
