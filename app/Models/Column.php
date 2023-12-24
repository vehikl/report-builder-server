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
        $normalized = preg_replace('/[0-9]+:,?/', '', $this->expression);
        return [str_replace(',', '.', $normalized)];
    }

    public function relations(): array
    {
        return array_map(function (string $path) {
            return implode('.', array_slice(explode('.', $path), 0, -1)) ?: null;
        }, $this->paths());
    }
}
