<?php

return [
    1 => [
        'table' => 'jobs',
        'name' => 'Job',
        'attributes' => [
            ['path' => 'code', 'name' => 'Code'],
            ['path' => 'title', 'name' => 'Title'],
        ],
        'relations' => [
            [
                'path' => 'employees',
                'name' => 'Employees',
                'related_entity_id' => 2,
                'is_collection' => true
            ]
        ]
    ],
    2 => [
        'table' => 'employees',
        'name' => 'Employee',
        'attributes' => [
            ['path' => 'id', 'name' => 'Id'],
            ['path' => 'name', 'name' => 'Name'],
            ['path' => 'salary', 'name' => 'Salary'],
            ['path' => 'bonus', 'name' => 'Bonus'],
            ['path' => 'manager_id', 'name' => 'Manager Id'],
            ['path' => 'job_code', 'name' => 'Job Code'],
        ],
        'relations' => [
            [
                'path' => 'job',
                'name' => 'Job',
                'related_entity_id' => 1,
                'is_collection' => false
            ],
            [
                'path' => 'manager',
                'name' => 'Manager',
                'related_entity_id' => 2,
                'is_collection' => false
            ],
            [
                'path' => 'subordinates',
                'name' => 'Subordinates',
                'related_entity_id' => 2,
                'is_collection' => true
            ],
            [
                'path' => null,
                'name' => 'Equity',
                'related_entity_id' => 3,
                'is_collection' => false
            ],
        ]
    ],
    3 =>[
        'table' => 'employees',
        'name' => 'Equity',
        'attributes' => [
            ['path' => 'equity_amount', 'name' => 'Amount'],
            ['path' => 'equity_rationale', 'name' => 'Rationale'],
        ],
        'relations' => []
    ],
];
