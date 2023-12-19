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

    public function path(): string
    {
        $normalized = preg_replace('/[0-9]+:/', '',  $this->expression);
        return str_replace(':', '.', $normalized);
    }

    public function relation(): string|null
    {
        return implode('.', array_slice(explode('.', $this->path()), 0, -1)) ?: null;
    }
}
