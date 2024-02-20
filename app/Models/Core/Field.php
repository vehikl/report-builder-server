<?php

namespace App\Models\Core;

use App\Utils\FieldType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => new FieldType($value)
        );
    }
}
