<?php

namespace App\Models\Core;

use App\Utils\Dependency\DependencyTree;
use App\Utils\Expressions\ExpressionContext;
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
        return $this->hasMany(Column::class)->orderBy('position');
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

    /** @param  array{key: string, direction: 'asc'|'dsc'}|null  $sort */
    public function getQuery(?array $sort): Builder
    {
        $model = new ($this->entity->getModelClass());

        $dependencyTree = DependencyTree::make($this->entity->getModel(), $this->getDataPaths());

        $query = QueryMaker::make($dependencyTree, new Path($model, null));

        $context = ExpressionContext::make($this->getSqlNames());

        $selects = $this->columns
            ->map(fn (Column $column) => DB::raw($column->getSelect($context)))
            ->toArray();

        return DB::query()
            ->from($query, $this->name)
            ->select($selects)
            ->when(
                $sort,
                fn (Builder $query, array $sort) => $query->orderBy($sort['key'], $sort['direction'])
            );
    }

    /** @param  array{key: string, direction: 'asc'|'dsc'}|null  $sort */
    public function preview(?array $sort): array
    {
        $query = $this->getQuery($sort);

        return [
            'name' => $this->name,
            'entity_id' => $this->entity_id,
            'columns' => $this->columns->map(fn (Column $column) => [
                'name' => $column->name,
                'key' => $column->key,
                'expression' => $column->expression->toArray(),
            ]),
            'records' => $query->paginate(40),
            'sort' => $sort,
        ];
    }

    /** @param  array{key: string, direction: 'asc'|'dsc'}|null  $sort */
    public function spreadsheet(?array $sort): array
    {
        return [
            $this->columns->map(fn (Column $column) => $column->name)->toArray(),
            ...$this->getQuery($sort)->get()->map(fn (object $record) => (array) $record),
        ];
    }
}
