<?php

namespace App\Models\Client\Enums;

enum PayRateType: string
{
    case Hourly = 'Hourly';
    case Salary = 'Salary';
}
