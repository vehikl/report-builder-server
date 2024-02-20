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

    public function getFieldDbPaths(): array
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

    public function getQuery(): Builder
    {
        $model = new ($this->entity->getModelClass());

        $dependencyTree = DependencyTree::make($this->entity->getModel(), $this->getFieldDbPaths());

        $query = QueryMaker::make($dependencyTree, new Path($model, null));

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
