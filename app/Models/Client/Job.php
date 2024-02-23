<?php

namespace App\Models\Client;

use App\Models\Core\CoreModel;
use App\Utils\Sql\SqlAttribute;
use App\Utils\Sql\SqlFn;
use App\Utils\Sql\SqlName;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends CoreModel
{
    use HasFactory;

    protected $fillable = ['title'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'job_code', 'code');
    }

    protected function displayName(): Attribute
    {
        return SqlAttribute::new(
            dependencies: ['code', 'title'],

            get: fn () => "$this->code $this->title",
            sql: fn (SqlName $code, SqlName $title) => SqlFn::CONCAT($code, ': ', $title)
        );
    }
}
