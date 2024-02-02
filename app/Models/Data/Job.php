<?php

namespace App\Models\Data;

use App\Utils\PhpAttributes\Dependencies;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends DataModel
{
    use HasFactory;

    protected $fillable = ['title'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'job_code', 'code');
    }

    #[Dependencies('code', 'title')]
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => "$this->code $this->title"
        );
    }
}
