<?php

namespace App\Models\Data;

use App\Utils\PhpAttributes\Dependencies;
use App\Utils\Sql\ExtendedAttribute;
use App\Utils\Sql\ExtendedBelongsTo;
use App\Utils\Sql\SqlFn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;

class Employee extends DataModel
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
            function (JoinClause $join, string $employeeManagerId, string $managerId) {
                $join->on($employeeManagerId, '=', $managerId);
            }
        );
    }

    public function job(): ExtendedBelongsTo
    {
        $relation = $this->extendedBelongsTo(Job::class, 'job_code', 'code');

        return $relation->withLeftJoin(
            ['job_code', 'job.code'],
            function (JoinClause $join, string $employeeJobCode, string $jobCode) {
                $join->on($employeeJobCode, '=', $jobCode);
            }
        );
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    #[Dependencies('salary', 'bonus')]
    protected function totalCompensation(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $this->salary + $this->bonus
        );
    }

    #[Dependencies('job.title')]
    protected function jobTitle(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $this->job->title
        );
    }

    #[Dependencies('salary')]
    protected function doubleSalary(): Attribute
    {
        return ExtendedAttribute::make(
            get: fn (mixed $value) => $this->salary * 2
        )
            ->withSql(['salary'], fn ($salary) => "$salary * 2");
    }

    #[Dependencies('bonus')]
    protected function multiplyBonus(float $times): float
    {
        return $this->bonus * $times;
    }

    #[Dependencies('name', 'job.title', 'job.code')]
    protected function nameWithJob(): Attribute
    {
        return ExtendedAttribute::make(
            get: fn () => "$this->name ({$this->job->code}: {$this->job->title})"
        )
            ->withSql(
                ['name', 'job.title', 'job.code'],
                function ($name, $jobTitle, $jobCode) {
                    return SqlFn::CONCAT($name, $jobTitle, $jobCode);
                });
    }

    #[Dependencies('name', 'job.display_name')]
    protected function nameWithJobDisplayName(): Attribute
    {
        return ExtendedAttribute::make(
            get: fn () => "$this->name ({$this->job->display_name})"
        )
            ->withSql(
                ['name', 'job.display_name'],
                function (string $name, string $jobDisplayName) {
                    return SqlFn::CONCAT($name, "'('", $jobDisplayName, "')'");
                });
    }
}
