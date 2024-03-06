<?php

namespace App\Models\Client;

use App\Models\Client\Enums\PayRateType;
use App\Models\Core\CoreModel;
use App\Utils\Sql\SqlAttribute;
use App\Utils\Sql\SqlContext;
use App\Utils\Sql\SqlName;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends CoreModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'family',
        'family_group',
        'ladder',
        'is_perf_eligible',
        'pay_rate_type',
    ];

    protected $casts = [
        'reports_to' => 'array',
        'pay_rate_type' => PayRateType::class,
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'job_code', 'code');
    }

    protected function isHourly(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->pay_rate_type === PayRateType::Hourly,

            dependencies: ['pay_rate_type'],
            sql: fn (SqlContext $ctx, SqlName $pay_rate_type) => "$pay_rate_type = ".PayRateType::Hourly->value
        );
    }

    protected function isSalary(): Attribute
    {
        return SqlAttribute::new(
            get: fn () => $this->pay_rate_type === PayRateType::Salary,

            dependencies: ['pay_rate_type'],
            sql: fn (SqlContext $ctx, SqlName $pay_rate_type) => "$pay_rate_type = ".PayRateType::Salary->value
        );
    }

    // -----------------------------------------

    protected function displayName(): Attribute
    {
        return SqlAttribute::new(
            dependencies: ['code', 'title'],

            get: fn () => "$this->code $this->title",
            sql: fn (SqlContext $ctx, SqlName $code, SqlName $title) => $ctx->CONCAT($code, ': ', $title)
        );
    }
}
