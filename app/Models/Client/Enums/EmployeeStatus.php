<?php

namespace App\Models\Client\Enums;

enum EmployeeStatus: string
{
    case Active = 'Active';
    case Contractor = 'Contractor';
    case OnLeave = 'On Leave';
    case Terminated = 'Terminated';
}
