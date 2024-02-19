<?php

namespace App\Models\Structure;

use App\Utils\DependencyTracker;
use App\Utils\Path;
use App\Utils\QueryMaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/** @property Entity $entity */
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

    public function getQueryStructure(): array
    {
        $structure = [
            'relation' => null,
            'columns' => [],
            'attributes' => [],
            'relations' => [],
        ];

        foreach ($this->dependencies() as $path) {
            $keys = explode('.', $path);
            $currentStructure = &$structure;
            $currentModel = $this->entity->getModel();

            foreach ($keys as $i => $key) {
                if ($i === array_key_last($keys)) {
                    if (DependencyTracker::isColumn($currentModel, $key)) {
                        $currentStructure['columns'][] = $key;

                        continue;
                    }

                    $currentStructure['attributes'][$key] = DependencyTracker::getSqlAttribute($currentModel, $key);

                    continue;
                }

                if (! isset($currentStructure['relations'][$key])) {
                    $currentStructure['relations'][$key] = [
                        'relation' => $currentModel->$key(),
                        'columns' => [],
                        'attributes' => [],
                        'relations' => [],
                    ];
                }

                $currentStructure = &$currentStructure['relations'][$key];
                $currentModel = $currentStructure['relation']->getRelated();
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

        return DB::query()->from($query, $this->name)->select($selects);
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
}
