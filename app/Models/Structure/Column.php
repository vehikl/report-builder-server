<?php

namespace App\Models\Structure;

use App\Utils\DependencyTracker;
use App\Utils\Expressions\Expression;
use App\Utils\FieldPath;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/** @property Expression $expression */
class Column extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'expression',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /** @return string[] */
    public function dependencies(): array
    {
        // TODO: cache this or receive as argument
        $fields = Field::query()->get();

        $fieldPaths = $this->expression->getFieldPaths();

        return array_merge(...array_map(
            function (string $path) use ($fields) {
                $Model = $this->report->entity->getModelClass();
                $dbPath = (new FieldPath($this->report->entity->id, $path))->toDbPath($fields);

                return DependencyTracker::getDependencies(new $Model(), $dbPath);
            },
            $fieldPaths
        ));
    }

    protected function expression(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Expression::make(json_decode($value, true)),
            set: fn (Expression|array $value) => json_encode(is_array($value) ? $value : $value->toArray()),
        );
    }

    protected function normalizedName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $allowedCharacters = str_split('abcdefghijklmnopqrstuvwxyz_ 0123456789');

                $isAllowed = fn (string $character) => in_array($character, $allowedCharacters);

                return Str::snake(implode('', array_filter(str_split(strtolower($this->name)), $isAllowed)));
            }
        );
    }

    protected function key(): Attribute
    {
        return Attribute::make(
            get: function () {
                $prefix = 'c'.str_pad($this->position, 4, '0', STR_PAD_LEFT);

                return "{$prefix}_$this->normalized_name";
            }
        );
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'expression' => $this->expression->toArray(),
            'key' => $this->key,
        ];
    }

    public function getSelect(): string
    {
        // TODO: cache this or receive as argument
        $fields = Field::query()->get();

        return "{$this->expression->toSql($this->report->entity, $fields)} as $this->key";
    }
}
