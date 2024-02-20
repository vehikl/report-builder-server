<?php

namespace App\Models\Core;

use App\Utils\Dependency\DependencyTree;
use App\Utils\Path;
use App\Utils\QueryMaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @property Entity $entity
 * @property Collection<int, Column> $columns
 */
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

    /** @return array<string, string> */
    public function getDataPaths(): array
    {
        $fields = Field::query()->get();

        return $this->columns
            ->flatMap(fn (Column $column) => $column->getDataPaths($fields))
            ->unique()
            ->toArray();
    }

    public function getSqlNames(): array
    {
        return Collection::make($this->getDataPaths())
            ->mapWithKeys(function (string $dataPath, string $fieldPath) {
                $sqlName = (new Path($this->entity->getModel(), null))->field($dataPath);

                return [$fieldPath => $sqlName];
            })
            ->toArray();
    }

    public function getQuery(): Builder
    {
        $model = new ($this->entity->getModelClass());

        $dependencyTree = DependencyTree::make($this->entity->getModel(), $this->getDataPaths());

        $query = QueryMaker::make($dependencyTree, new Path($model, null));

        $sqlNames = $this->getSqlNames();

        $selects = $this->columns
            ->map(fn (Column $column) => DB::raw($column->getSelect($sqlNames)))
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
            'records' => $this->getQuery()->take(20)->get(),
        ];
    }
}
