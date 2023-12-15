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
}
