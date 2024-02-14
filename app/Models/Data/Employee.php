<?php

namespace App\Models\Data;

use App\Utils\PhpAttributes\Dependencies;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_code', 'code');
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
        return Attribute::make(
            get: fn (mixed $value) => $this->salary * 2
        );
    }

    #[Dependencies('bonus')]
    protected function multiplyBonus(float $times): float
    {
        return $this->bonus * $times;
    }

    #[Dependencies('name', 'job.title', 'job.code')]
    protected function nameWithJob(): Attribute
    {
        return Attribute::make(
            get: fn () => "$this->name ({$this->job->code}: {$this->job->title})"
        );
    }
}
