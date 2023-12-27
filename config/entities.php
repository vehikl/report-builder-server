<?php

return [
    1 => [
        'table' => 'jobs',
        'name' => 'Job',
        'attributes' => [
            /*  1 */['path' => 'code', 'name' => 'Code', 'type' => 'string'],
            /*  2 */['path' => 'title', 'name' => 'Title', 'type' => 'string'],
            /*  3 */['path' => 'employees', 'name' => 'Employees', 'type' => 'collection:2']
        ],
    ],
    2 => [
        'table' => 'employees',
        'name' => 'Employee',
        'attributes' => [
            /*  4 */['path' => 'id', 'name' => 'Id', 'type' => 'number'],
            /*  5 */['path' => 'name', 'name' => 'Name', 'type' => 'string'],
            /*  6 */['path' => 'salary', 'name' => 'Salary', 'type' => 'number'],
            /*  7 */['path' => 'bonus', 'name' => 'Bonus', 'type' => 'number'],
            /*  8 */['path' => 'manager_id', 'name' => 'Manager Id', 'type' => 'number'],
            /*  9 */['path' => 'job_code', 'name' => 'Job Code', 'type' => 'string'],
            /* 10 */['path' => 'job', 'name' => 'Job', 'type' => 'entity:1'],
            /* 11 */['path' => 'manager', 'name' => 'Manager', 'type' => 'entity:2'],
            /* 12 */['path' => 'subordinates', 'name' => 'Subordinates', 'type' => 'collection:2'],
            /* 13 */['path' => null, 'name' => 'Equity', 'type' => 'entity:3'],
        ],
    ],
    3 =>[
        'table' => 'employees',
        'name' => 'Equity',
        'attributes' => [
            /* 14 */['path' => 'equity_amount', 'name' => 'Amount', 'type' => 'number'],
            /* 15 */['path' => 'equity_rationale', 'name' => 'Rationale', 'type' => 'string'],
        ],
    ],
];
