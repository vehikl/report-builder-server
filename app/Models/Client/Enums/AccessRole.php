<?php

namespace App\Models\Client\Enums;

enum AccessRole: string
{
    case SuperAdmin = 'SuperAdmin';
    case Manager = 'Manager';
}
