<?php

namespace App\Models\Structure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = ['table', 'name'];

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }
}
