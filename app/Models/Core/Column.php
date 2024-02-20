<?php

namespace App\Models\Core;

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

    protected function expression(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Expression::make(json_decode($value, true)),
            set: fn (Expression|array $value) => json_encode(is_array($value) ? $value : $value->toArray()),
        );
    }

    /**
     * @param  Collection<int, Field>  $fields
     * @return array<string, string>
     */
    public function getDataPaths(Collection $fields): array
    {
        return Collection::make($this->expression->getFieldPaths())
            ->unique()
            ->mapWithKeys(function (string $fieldPath) use ($fields) {
                $dataPath = (new FieldPath($this->report->entity->id, $fieldPath))->toDataPath($fields);

                return [$fieldPath => $dataPath];
            })
            ->toArray();
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

    /** @param  array<string, string>  $sqlNames */
    public function getSelect(array $sqlNames): string
    {
        return "{$this->expression->toSql($sqlNames)} as $this->key";
    }
}
