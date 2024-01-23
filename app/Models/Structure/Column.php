<?php

namespace App\Models\Structure;

use App\Utils\Expressions\Expression;
use Illuminate\Database\Eloquent\Casts\Attribute as ModelAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function relations(): array
    {
        // TODO: cache this or receive as argument
        $attributes = Attribute::query()->get();

        $dbPaths = $this->expression->getDbPaths($this->report->entity_id, $attributes);

        return array_map(function (string $path) {
            return implode('.', array_slice(explode('.', $path), 0, -1)) ?: null;
        }, $dbPaths);
    }

    protected function expression(): ModelAttribute
    {
        return ModelAttribute::make(
            get: fn (string $value) => Expression::make(json_decode($value, true)),
            set: fn (Expression|array $value) => json_encode(is_array($value) ? $value : $value->toArray()),
        );
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'expression' => $this->expression->toArray(),
        ];
    }
}
