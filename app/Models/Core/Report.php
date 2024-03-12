<?php

namespace App\Models\Core;

use App\Models\Client\User;
use App\Utils\Dependency\DependencyTree;
use App\Utils\Expressions\ExpressionContext;
use App\Utils\PathResolver;
use App\Utils\QueryMaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Benchmark;
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

    /** @return Collection<int, Column> */
    public function getColumnsFor(User $user): Collection
    {
        $fields = Field::query()->get();

        return $this->columns->filter(fn (Column $column) => $column->canAccess('view', $user, $fields));
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
                $sqlName = (new PathResolver($this->entity->getModel(), null))->field($dataPath);

                return [$fieldPath => $sqlName];
            })
            ->toArray();
    }

    /** @param  array{key: string, direction: 'asc'|'dsc'}|null  $sort */
    public function getQuery(?array $sort): Builder
    {
        $model = new ($this->entity->getModelClass());

        [$dependencyTree, $dependencyTreeDuration] = Benchmark::value(fn () => DependencyTree::make($this->entity->getModel(), $this->getDataPaths()));

        [$query, $queryDuration] = Benchmark::value(fn () => QueryMaker::make($dependencyTree, new PathResolver($model, null)));

        [$context, $contextDuration] = Benchmark::value(fn () => ExpressionContext::make($this->getSqlNames()));

        [$selects, $selectsDuration] = Benchmark::value(
            fn () => $this->getColumnsFor(User::first())
                ->map(fn (Column $column) => DB::raw($column->getSelect($context)))
                ->toArray()
        );

        logger('Report::getQuery', [
            'dependencyTree' => $dependencyTreeDuration,
            'query' => $queryDuration,
            'context' => $contextDuration,
            'selects' => $selectsDuration,
            'total' => $dependencyTreeDuration + $queryDuration + $contextDuration + $selectsDuration,
        ]);

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
        logger('-------');

        /** @var Builder $query */
        [$query, $queryDuration] = Benchmark::value(fn () => $this->getQuery($sort));
        [$pagination, $paginationDuration] = Benchmark::value(fn () => $query->paginate(40));

        logger('Report::preview', [
            'query' => $queryDuration,
            'fetch' => $paginationDuration,
            'total' => $queryDuration + $paginationDuration,
        ]);

        return [
            'name' => $this->name,
            'entity_id' => $this->entity_id,
            'columns' => $this->getColumnsFor(User::first())->map(fn (Column $column) => [
                'name' => $column->name,
                'key' => $column->key,
                'expression' => $column->expression->toArray(),
                'format' => $column->format->value,
            ])->toArray(),
            'records' => $pagination,
            'sort' => $sort,
        ];
    }

    /** @param  array{key: string, direction: 'asc'|'dsc'}|null  $sort */
    public function spreadsheet(?array $sort): array
    {
        [$query, $queryDuration] = Benchmark::value(fn () => $this->getQuery($sort));
        [$data, $dataDuration] = Benchmark::value(fn () => $query->get());

        logger('Report::spreadsheet', [
            'query' => $queryDuration,
            'fetch' => $dataDuration,
            'total' => $queryDuration + $dataDuration,
        ]);

        return [
            'headers' => $this->getColumnsFor(User::first())->mapWithKeys(fn (Column $column) => [$column->name => $column->format->toExcel()])->toArray(),
            'records' => $data->map(fn (object $record) => (array) $record)->toArray(),
        ];
    }
}
