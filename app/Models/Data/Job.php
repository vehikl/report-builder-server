<?php

namespace App\Models\Data;

use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\SqlFn;
use App\Utils\Sql\SqlName;
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

    protected function displayName(): Attribute
    {
        return ExtendedAttribute::make(
            get: fn () => "$this->code $this->title"
        )
            ->withSql(
                ['code', 'title'],
                fn (SqlName $code, SqlName $title) => SqlFn::CONCAT($code, ': ', $title)
            );
    }
}
