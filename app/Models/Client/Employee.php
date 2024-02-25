<?php

namespace App\Models\Client;

use App\Models\Core\CoreModel;
use App\Utils\Relations\BelongsToList;
use App\Utils\Relations\BelongsToListItem;
use App\Utils\Relations\JoinableBelongsTo;
use App\Utils\Sql\JoinContext;
use App\Utils\Sql\SqlAttribute;
use App\Utils\Sql\SqlContext;
use App\Utils\Sql\SqlName;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends CoreModel
{
    use HasFactory;

    protected $fillable = [
        'display_name',
        'salary',
        'bonus',
        'manager_id',
        'job_code',
    ];

    protected $casts = [
        'reports_to' => 'array',
    ];

    public function manager(): JoinableBelongsTo
    {
        $relation = $this->joinableBelongsTo(Employee::class, 'manager_id');

        return $relation->withJoin(
            ['manager_id', 'manager.id'],
            function (JoinContext $ctx, SqlName $employeeManagerId, SqlName $managerId) {
                $ctx->join->on($employeeManagerId, '=', $managerId);
            }
        );
    }

    public function managers(): BelongsToList
    {
        return $this->belongsToList(Employee::class, 'reports_to')->respectingListOrder();
    }

    public function elt(): BelongsToListItem
    {
        $index = 1;
        $relation = $this->belongsToListItem(Employee::class, 'reports_to', $index);

        return $relation->withJoin(
            ['elt.id', 'reports_to'],
            function (JoinContext $ctx, SqlName $eltId, SqlName $reportsTo) use ($index) {
                $ctx->join->on($eltId, '=', "$reportsTo->[$index]");
            }
        );
    }

    public function eltPlus1(): BelongsToListItem
    {
        $index = 2;
        $relation = $this->belongsToListItem(Employee::class, 'reports_to', $index);

        return $relation->withJoin(
            ['eltPlus1.id', 'reports_to'],
            function (JoinContext $ctx, SqlName $eltId, SqlName $reportsTo) use ($index) {
                $ctx->join->on($eltId, '=', "$reportsTo->[$index]");
            }
        );
    }

    public function job(): JoinableBelongsTo
    {
        $relation = $this->joinableBelongsTo(Job::class, 'job_code', 'code');

        return $relation->withJoin(
            ['job_code', 'job.code'],
            function (JoinContext $ctx, SqlName $employeeJobCode, SqlName $jobCode) {
                $ctx->join->on($employeeJobCode, '=', $jobCode);
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
            sql: fn (SqlContext $ctx, SqlName $salary, SqlName $bonus) => "$salary + $bonus"
        );
    }

    protected function jobTitle(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->job->title,

            dependencies: ['job.title'],
            sql: fn (SqlContext $ctx, SqlName $jobTitle) => $jobTitle
        );
    }

    protected function doubleSalary(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->salary * 2,

            dependencies: ['salary'],
            sql: fn (SqlContext $ctx, SqlName $salary) => "$salary * 2"
        );
    }

    protected function nameWithJob(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => "$this->name ({$this->job->code}: {$this->job->title})",

            dependencies: ['display_name', 'job.code', 'job.title'],
            sql: function (SqlContext $ctx, SqlName $name, SqlName $jobCode, SqlName $jobTitle) {
                return $ctx->CONCAT($name, ' (', $jobCode, ': ', $jobTitle, ')');
            }
        );
    }

    protected function nameWithJobDisplayName(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => "$this->name ({$this->job->display_name})",

            dependencies: ['display_name', 'job.display_name'],
            sql: function (SqlContext $ctx, SqlName $name, SqlName $jobDisplayName) {
                return $ctx->CONCAT($name, ' ', '(', $jobDisplayName, ')');
            }
        );
    }
}
