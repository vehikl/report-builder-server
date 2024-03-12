<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['table', 'name'];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    /** @return class-string<Model> */
    public function getModelClass(): string
    {
        return config('models')[$this->getAttribute('table')];
    }

    public function getModel(): Model
    {
        return new ($this->getModelClass());
    }
}
