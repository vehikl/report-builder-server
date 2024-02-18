<?php

return [
    1 => [
        'table' => 'jobs',
        'name' => 'Job',
        'fields' => [
            /*  1 */ ['identifier' => 'code', 'path' => 'code', 'name' => 'Code', 'type' => 'string'],
            /*  2 */ ['identifier' => 'title', 'path' => 'title', 'name' => 'Title', 'type' => 'string'],
            /*  3 */ ['identifier' => 'employees', 'path' => 'employees', 'name' => 'Employees', 'type' => 'collection:2'],
        ],
    ],
    2 => [
        'table' => 'employees',
        'name' => 'Employee',
        'fields' => [
            /*  4 */ ['identifier' => 'id', 'path' => 'id', 'name' => 'Id', 'type' => 'number'],
            /*  5 */ ['identifier' => 'name', 'path' => 'name', 'name' => 'Name', 'type' => 'string'],
            /*  6 */ ['identifier' => 'salary', 'path' => 'salary', 'name' => 'Salary', 'type' => 'number'],
            /*  7 */ ['identifier' => 'bonus', 'path' => 'bonus', 'name' => 'Bonus', 'type' => 'number'],
            /*  8 */ ['identifier' => 'manager_id', 'path' => 'manager_id', 'name' => 'Manager Id', 'type' => 'number'],
            /*  9 */ ['identifier' => 'job_code', 'path' => 'job_code', 'name' => 'Job Code', 'type' => 'string'],
            /* 10 */ ['identifier' => 'total_compensation', 'path' => 'total_compensation', 'name' => 'Total Compensation', 'type' => 'number'],
            /* 11 */ ['identifier' => 'name_with_job', 'path' => 'name_with_job', 'name' => 'Name With Job', 'type' => 'string'],
            /* 12 */ ['identifier' => 'name_with_job_display_name', 'path' => 'name_with_job_display_name', 'name' => 'Name With Job Display Name', 'type' => 'string'],
            /* 13 */ ['identifier' => 'job_title', 'path' => 'job_title', 'name' => 'Job Title', 'type' => 'string'],
            /* 14 */ ['identifier' => 'job', 'path' => 'job', 'name' => 'Job', 'type' => 'entity:1'],
            /* 15 */ ['identifier' => 'manager', 'path' => 'manager', 'name' => 'Manager', 'type' => 'entity:2'],
            /* 16 */ ['identifier' => 'subordinates', 'path' => 'subordinates', 'name' => 'Subordinates', 'type' => 'collection:2'],
            /* 17 */ ['identifier' => 'equity', 'path' => null, 'name' => 'Equity', 'type' => 'entity:3'],
        ],
    ],
    3 => [
        'table' => 'employees',
        'name' => 'Equity',
        'fields' => [
            /* 14 */ ['identifier' => 'amount', 'path' => 'equity_amount', 'name' => 'Amount', 'type' => 'number'],
            /* 15 */ ['identifier' => 'rationale', 'path' => 'equity_rationale', 'name' => 'Rationale', 'type' => 'string'],
        ],
    ],
];
