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

    public function relation(): string|null
    {
        return implode('.', array_slice(explode('.', $this->expression), 0, -1)) ?: null;
    }
}
