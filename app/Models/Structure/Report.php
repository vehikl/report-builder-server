<?php

namespace App\Models\Structure;

use App\Utils\Dependency\DependencyTree;
use App\Utils\FieldPath;
use App\Utils\Path;
use App\Utils\QueryMaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
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

    public function getFieldsDbPaths(): array
    {
        $fields = Field::query()->get();

        return $this->columns
            ->flatMap(fn (Column $column) => $column->expression->getFieldPaths())
            ->mapWithKeys(function (string $fieldPath) use ($fields) {
                $dbPath = (new FieldPath($this->entity->id, $fieldPath))->toDbPath($fields);

                return [$fieldPath => $dbPath];
            })
            ->unique()
            ->toArray();
    }

    public function getFieldsSqlNames(): array
    {
        return Collection::make($this->getFieldsDbPaths())
            ->mapWithKeys(function (string $dbPath, string $fieldPath) {
                $sqlName = (new Path($this->entity->getModel(), null))->field($dbPath);
                return [$fieldPath => $sqlName];
            })
            ->toArray();
    }

    public function getQuery(): Builder
    {
        $model = new ($this->entity->getModelClass());

        $dependencyTree = DependencyTree::make($this->entity->getModel(), $this->getFieldsDbPaths());

        $query = QueryMaker::make($dependencyTree, new Path($model, null));

        $fieldsSqlNames = $this->getFieldsSqlNames();

        $selects = $this->columns
            ->map(fn (Column $column) => DB::raw($column->getSelect($fieldsSqlNames)))
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
