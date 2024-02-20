<?php

namespace App\Models\Client;

use App\Models\Core\CoreModel;
use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\ExtendedBelongsTo;
use App\Utils\Sql\SqlFn;
use App\Utils\Sql\SqlName;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;

class Employee extends CoreModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'salary',
        'bonus',
        'manager_id',
        'job_code',
    ];

    public function manager(): ExtendedBelongsTo
    {
        $relation = $this->extendedBelongsTo(Employee::class, 'manager_id');

        return $relation->withLeftJoin(
            ['manager_id', 'manager.id'],
            function (JoinClause $join, SqlName $employeeManagerId, SqlName $managerId) {
                $join->on($employeeManagerId, '=', $managerId);
            }
        );
    }

    public function job(): ExtendedBelongsTo
    {
        $relation = $this->extendedBelongsTo(Job::class, 'job_code', 'code');

        return $relation->withLeftJoin(
            ['job_code', 'job.code'],
            function (JoinClause $join, SqlName $employeeJobCode, SqlName $jobCode) {
                $join->on($employeeJobCode, '=', $jobCode);
            }
        );
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    protected function totalCompensation(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->salary + $this->bonus
        );
    }

    protected function jobTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->job->title
        );
    }

    protected function doubleSalary(): Attribute
    {
        return ExtendedAttribute::make(
            get: fn () => $this->salary * 2
        )
            ->withSql(['salary'], fn (SqlName $salary) => "$salary * 2");
    }

    protected function nameWithJob(): Attribute
    {
        return ExtendedAttribute::make(
            get: fn () => "$this->name ({$this->job->code}: {$this->job->title})"
        )
            ->withSql(
                ['name', 'job.title', 'job.code'],
                function (SqlName $name, SqlName $jobTitle, SqlName $jobCode) {
                    return SqlFn::CONCAT($name, ' ', $jobTitle, ' ', $jobCode);
                });
    }

    protected function nameWithJobDisplayName(): Attribute
    {
        return ExtendedAttribute::make(
            get: fn () => "$this->name ({$this->job->display_name})"
        )
            ->withSql(
                ['name', 'job.display_name'],
                function (SqlName $name, SqlName $jobDisplayName) {
                    return SqlFn::CONCAT($name, ' ', '(', $jobDisplayName, ')');
                });
    }
}
