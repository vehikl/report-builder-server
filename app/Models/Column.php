<?php

namespace App\Models;

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

    public function paths(): array
    {
        $attributes = Attribute::query()->get();
        $identifiers = explode('.', $this->expression);

        $paths = [];
        $currentEntityId = $this->report->entity_id;
        foreach ($identifiers as $identifier) {
            $attribute = $attributes->where('entity_id', $currentEntityId)->where('identifier', $identifier)->first();
            $paths[] = $attribute->path;

            $currentEntityId = match ($attribute->type->name) {
                'entity', 'collection' => $attribute->type->entityId,
                default => null
            };
        }

        return [implode('.', array_filter($paths))];
    }

    public function relations(): array
    {
        return array_map(function (string $path) {
            return implode('.', array_slice(explode('.', $path), 0, -1)) ?: null;
        }, $this->paths());
    }
}
