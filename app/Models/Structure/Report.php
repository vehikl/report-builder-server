<?php

namespace App\Models\Structure;

use App\Utils\Dependency\DependencyRelation;
use App\Utils\Dependency\DependencyTree;
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

    public function getDependencyTree(): DependencyTree
    {
        $tree = new DependencyTree($this->entity->getModel());

        foreach ($this->dependencies() as $path) {
            $keys = explode('.', $path);
            $currentTree = $tree;

            foreach ($keys as $i => $key) {
                if ($i === array_key_last($keys)) {
                    if (DependencyTracker::isColumn($currentTree->model, $key)) {
                        $currentTree->columns[] = $key;

                        continue;
                    }

                    $currentTree->attributes[$key] = DependencyTracker::getSqlAttribute($currentTree->model, $key);

                    continue;
                }

                if (! array_key_exists($key, $currentTree->relations)) {
                    $currentTree->relations[$key] = new DependencyRelation($currentTree->model->$key());
                }

                $currentTree = $currentTree->relations[$key]->tree;
            }
        }

        return $tree;

    }

    public function getQuery(): Builder
    {
        $model = new ($this->entity->getModelClass());

        $query = QueryMaker::make($this->getDependencyTree(), new Path($model, null));

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
