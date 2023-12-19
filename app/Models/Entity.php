<?php

namespace App\Models;

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

    public function relations(): HasMany
    {
        return $this->hasMany(Relation::class);
    }
}
