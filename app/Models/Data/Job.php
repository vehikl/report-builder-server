<?php

namespace App\Models\Data;

use App\Utils\PhpAttributes\Dependencies;
use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\SqlFn;
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
        return ExtendedAttribute::make(
            get: fn (mixed $value) => "$this->code $this->title"
        )
            ->withSql(['code', 'title'], fn (string $code, $title) => SqlFn::CONCAT($code, $title));
    }
}
