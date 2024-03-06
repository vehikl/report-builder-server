<?php

namespace App\Models\Client;

use App\Models\Client\Enums\EmployeeProgram;
use App\Models\Client\Enums\EmployeeStatus;
use App\Models\Core\CoreModel;
use App\Utils\Relations\BelongsToList;
use App\Utils\Relations\BelongsToListItem;
use App\Utils\Relations\HasManyThroughList;
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
        'email',
        'hire_date',
        'company_full',
        'termination_date',
        'status',
        'role_type',
        'salary',
        'algo_salary',
        'new_salary',
        'location',
        'country',
        'currency_code',
        'reports_to',
        'manager_id',
        'job_code',
        'promo_job_code',
        'new_job_code',
        'bonus',
        'equity_amount',
        'equity_rationale',
    ];

    protected $casts = [
        'reports_to' => 'array',
        'role_type' => EmployeeProgram::class,
        'status' => EmployeeStatus::class,
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

    public function org(): HasManyThroughList
    {
        return $this->hasManyThroughList(Employee::class, 'reports_to');
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

    public function currency(): JoinableBelongsTo
    {
        $relation = $this->joinableBelongsTo(Currency::class, 'currency_code', 'code');

        return $relation->withJoin(
            ['currency_code', 'currency.code'],
            function (JoinContext $ctx, SqlName $employeeCurrencyCode, SqlName $currencyCode) {
                $ctx->join->on($employeeCurrencyCode, '=', $currencyCode);
            }
        );
    }

    public function promoJob(): JoinableBelongsTo
    {
        $relation = $this->joinableBelongsTo(Job::class, 'promo_job_code', 'code');

        return $relation->withJoin(
            ['promo_job_code', 'promoJob.code'],
            function (JoinContext $ctx, SqlName $employeePromoJobCode, SqlName $promoJobCode) {
                $ctx->join->on($employeePromoJobCode, '=', $promoJobCode);
            }
        );
    }

    public function newJob(): JoinableBelongsTo
    {
        $relation = $this->joinableBelongsTo(Job::class, 'new_job_code', 'code');

        return $relation->withJoin(
            ['new_job_code', 'newJob.code'],
            function (JoinContext $ctx, SqlName $employeePromoJobCode, SqlName $promoJobCode) {
                $ctx->join->on($employeePromoJobCode, '=', $promoJobCode);
            }
        );
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    protected function isSundeep(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->id === 999,

            dependencies: ['id'],
            sql: fn (SqlContext $ctx, SqlName $id) => "$id = 999"
        );
    }

    protected function program(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->elt->is_sundepp ? EmployeeProgram::Eng : $this->role_type,

            dependencies: ['role_type', 'elt.is_sundeep'],
            sql: fn (SqlContext $ctx, SqlName $role_type, SqlName $is_sundeep) => $ctx->IF(
                $is_sundeep,
                EmployeeProgram::Eng->value,
                $role_type
            )
        );
    }

    protected function isTermed(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->termination_date !== null,

            dependencies: ['termination_date'],
            sql: fn (SqlContext $ctx, SqlName $termination_date) => $ctx->notNull($termination_date)
        );
    }

    protected function isOnLeave(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->status === EmployeeStatus::OnLeave,

            dependencies: ['status'],
            sql: fn (SqlContext $ctx, SqlName $status) => "$status = ".EmployeeStatus::OnLeave->value
        );
    }

    protected function hasPromotion(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->promo_job_code !== null,

            dependencies: ['promo_job_code'],
            sql: fn (SqlContext $ctx, SqlName $promo_job_code) => $ctx->notNull($promo_job_code)
        );
    }

    protected function canHaveHourlyJob(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => in_array(substr($this->location, 0, 3), ['USA', 'CAN']),

            dependencies: ['location'],
            sql: fn (SqlContext $ctx, SqlName $location) => $ctx->arrayContains(
                ['USA', 'CAN'],
                $ctx->SUBSTRING($location, 0, 3)
            )
        );
    }

    protected function isPromoHourlyToSalary(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->can_have_hourly_job && $this->job->is_hourly && $this->promoJob?->is_salary,

            dependencies: ['can_have_hourly_job', 'job.is_hourly', 'promoJob.is_salary'],
            sql: fn (
                SqlContext $ctx,
                SqlName $can_have_hourly_job,
                SqlName $job_is_hourly,
                SqlName $promoJob_is_salary
            ) => "$can_have_hourly_job AND $job_is_hourly AND $promoJob_is_salary"
        );
    }

    protected function isPromoSalaryToHourly(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->can_have_hourly_job && $this->job->is_salary && $this->promoJob->is_hourly,

            dependencies: ['can_have_hourly_job', 'job.is_salary', 'promoJob.is_hourly'],
            sql: fn (
                SqlContext $ctx,
                SqlName $can_have_hourly_job,
                SqlName $job_is_salary,
                SqlName $promoJob_is_hourly
            ) => "$can_have_hourly_job AND $job_is_salary AND $promoJob_is_hourly"
        );
    }

    protected function salaryUsd(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->salary * $this->fx_to_usd,

            dependencies: ['salary', 'currency.fx_to_usd'],
            sql: fn (SqlContext $ctx, SqlName $salary, SqlName $fx_to_usd) => "$salary * $fx_to_usd"
        );
    }

    protected function algoSalaryUsd(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->algo_salary * $this->fx_to_usd,

            dependencies: ['algo_salary', 'currency.fx_to_usd'],
            sql: fn (SqlContext $ctx, SqlName $algo_salary, SqlName $fx_to_usd) => "$algo_salary * $fx_to_usd"
        );
    }

    protected function newSalaryUsd(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->salary * $this->fx_to_usd,

            dependencies: ['new_salary', 'currency.fx_to_usd'],
            sql: fn (SqlContext $ctx, SqlName $new_salary, SqlName $fx_to_usd) => "$new_salary * $fx_to_usd"
        );
    }

    protected function salaryIncreaseAmount(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->new_salary - $this->salary,

            dependencies: ['salary', 'new_salary'],
            sql: fn (SqlContext $ctx, SqlName $salary, SqlName $new_salary) => "$new_salary - $salary"
        );
    }

    protected function salaryIncreaseAmountUsd(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->salary_increase_amount * $this->fx_to_usd,

            dependencies: ['salary_increase_amount', 'currency.fx_to_usd'],
            sql: fn (SqlContext $ctx, SqlName $salary_increase_amount, SqlName $fx_to_usd) => "$salary_increase_amount * $fx_to_usd"
        );
    }

    protected function salaryIncreasePercent(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->salary_increase_amount / $this->salary,

            dependencies: ['salary_increase_amount', 'salary'],
            sql: fn (SqlContext $ctx, SqlName $salary_increase_amount, SqlName $salary) => "$salary_increase_amount / $salary"
        );
    }

    // -----------------------------------------

    protected function nameWithStatus(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->salary + $this->bonus,

            dependencies: ['salary', 'bonus'],
            sql: fn (SqlContext $ctx, SqlName $salary, SqlName $bonus) => "$salary + $bonus"
        );
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
