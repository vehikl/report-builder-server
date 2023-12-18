<?php

return [
    [
        'table' => 'jobs',
        'name' => 'Job',
        'attributes' => [
            ['column' => 'code', 'name' => 'Code'],
            ['column' => 'title', 'name' => 'Title'],
        ],
        'relations' => [
            [
                'accessor' => 'employees',
                'name' => 'Employees',
                'related_entity_table' => 'employees',
                'is_collection' => true
            ]
        ]
    ],
    [
        'table' => 'employees',
        'name' => 'Employee',
        'attributes' => [
            ['column' => 'id', 'name' => 'Id'],
            ['column' => 'name', 'name' => 'Name'],
            ['column' => 'salary', 'name' => 'Salary'],
            ['column' => 'bonus', 'name' => 'Bonus'],
            ['column' => 'manager_id', 'name' => 'Manager Id'],
            ['column' => 'job_code', 'name' => 'Job Code'],
        ],
        'relations' => [
            [
                'accessor' => 'job',
                'name' => 'Job',
                'related_entity_table' => 'jobs',
                'is_collection' => false
            ],
            [
                'accessor' => 'manager',
                'name' => 'Manager',
                'related_entity_table' => 'employees',
                'is_collection' => false
            ],
            [
                'accessor' => 'subordinates',
                'name' => 'Subordinates',
                'related_entity_table' => 'employees',
                'is_collection' => true
            ]
        ]
    ]
];
