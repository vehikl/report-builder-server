<?php

namespace App\Models\Client;

use App\Models\Core\CoreModel;
use App\Utils\Sql\ExtendedBelongsTo;
use App\Utils\Sql\SqlAttribute;
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

        return $relation->withJoin(
            ['manager_id', 'manager.id'],
            function (JoinClause $join, SqlName $employeeManagerId, SqlName $managerId) {
                $join->on($employeeManagerId, '=', $managerId);
            }
        );
    }

    public function job(): ExtendedBelongsTo
    {
        $relation = $this->extendedBelongsTo(Job::class, 'job_code', 'code');

        return $relation->withJoin(
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
        return SqlAttribute::new(
            get: fn () => $this->salary + $this->bonus,

            dependencies: ['salary', 'bonus'],
            sql: fn (SqlName $salary, SqlName $bonus) => "$salary + $bonus"
        );
    }

    protected function jobTitle(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->job->title,

            dependencies: ['job.title'],
            sql: fn (SqlName $jobTitle) => $jobTitle
        );
    }

    protected function doubleSalary(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->salary * 2,

            dependencies: ['salary'],
            sql: fn (SqlName $salary) => "$salary * 2"
        );
    }

    protected function nameWithJob(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => "$this->name ({$this->job->code}: {$this->job->title})",

            dependencies: ['name', 'job.title', 'job.code'],
            sql: function (SqlName $name, SqlName $jobTitle, SqlName $jobCode) {
                return SqlFn::CONCAT($name, ' ', $jobTitle, ' ', $jobCode);
            }
        );
    }

    protected function nameWithJobDisplayName(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => "$this->name ({$this->job->display_name})",

            dependencies: ['name', 'job.display_name'],
            sql: function (SqlName $name, SqlName $jobDisplayName) {
                return SqlFn::CONCAT($name, ' ', '(', $jobDisplayName, ')');
            }
        );
    }
}
