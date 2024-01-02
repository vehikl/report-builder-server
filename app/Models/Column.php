<?php

namespace App\Models;

use App\Utils\AttributePath;
use App\Utils\ExpressionParser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Column extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'expression'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function relations(): array
    {
        return array_map(function (string $path) {
            return implode('.', array_slice(explode('.', $path), 0, -1)) ?: null;
        }, $this->dbPaths());
    }

    public function ast(): array
    {
        return (new ExpressionParser())->read($this->expression);
    }

    private function dbPaths(): array
    {
        $attributePaths = $this->getAttributePaths($this->ast());

        // TODO: cache this or receive as argument
        $attributes = Attribute::query()->get();

        return array_map(fn(AttributePath $path) => $path->toDbPath($attributes), $attributePaths);
    }

    private function getAttributePaths(array $node): array
    {
        return match ($node['type']) {
            'binary' => [
                ...$this->getAttributePaths($node['left']),
                ...$this->getAttributePaths($node['right'])
            ],
            'call' => array_merge(...array_map(
                fn(array $argNode) => $this->getAttributePaths($argNode),
                $node['args']
            )),
            'attribute' => [new AttributePath($this->report->entity_id, $node['value'])],
            default => []
        };
    }
}
