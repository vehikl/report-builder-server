<?php

return [
    1 => [
        'table' => 'jobs',
        'name' => 'Job',
        'fields' => [
            /*  1 */ ['identifier' => 'code', 'path' => 'code', 'name' => 'Code', 'type' => 'string'],
            /*  2 */ ['identifier' => 'title', 'path' => 'title', 'name' => 'Title', 'type' => 'string'],
            /*  3 */ ['identifier' => 'ladder', 'path' => 'ladder', 'name' => 'Ladder', 'type' => 'string'],
            /*  4 */ ['identifier' => 'family', 'path' => 'family', 'name' => 'Family', 'type' => 'string'],
            /*  5 */ ['identifier' => 'family_group', 'path' => 'family_group', 'name' => 'Family Group', 'type' => 'string'],
            /*  6 */ ['identifier' => 'employees', 'path' => 'employees', 'name' => 'Employees', 'type' => 'collection:2'],
        ],
    ],
    2 => [
        'table' => 'employees',
        'name' => 'Employee',
        'fields' => [
            /*  7 */ ['identifier' => 'id', 'path' => 'id', 'name' => 'Id', 'type' => 'number'],
            /*  8 */ ['identifier' => 'display_name', 'path' => 'display_name', 'name' => 'Name', 'type' => 'string'],

            /*  9 */ ['identifier' => 'program', 'path' => 'program', 'name' => 'Program', 'type' => 'string'],
            /* 10 */ ['identifier' => 'hire_date', 'path' => 'hire_date', 'name' => 'Hire Date', 'type' => 'string'],
            /* 11 */ ['identifier' => 'is_termed', 'path' => 'is_termed', 'name' => 'Is Termed', 'type' => 'string'],
            /* 12 */ ['identifier' => 'currency_code', 'path' => 'currency_code', 'name' => 'Currency Code', 'type' => 'string'],
            /* 13 */ ['identifier' => 'currency_fx_to_usd', 'path' => 'currency.fx_to_usd', 'name' => 'Currency Fx To Usd', 'type' => 'number'],
            /* 14 */ ['identifier' => 'has_promotion', 'path' => 'has_promotion', 'name' => 'Has Promotion', 'type' => 'boolean'],
            /* 15 */ ['identifier' => 'is_promo_hourly_to_salary', 'path' => 'is_promo_hourly_to_salary', 'name' => 'Is Promo Hourly To Salary', 'type' => 'boolean'],
            /* 16 */ ['identifier' => 'is_promo_salary_to_hourly', 'path' => 'is_promo_salary_to_hourly', 'name' => 'Is Promo Salary To Hourly', 'type' => 'boolean'],
            /* 17 */ ['identifier' => 'salary', 'path' => 'salary', 'name' => 'Salary', 'type' => 'number'],
            /* 18 */ ['identifier' => 'salary_usd', 'path' => 'salary_usd', 'name' => 'Salary Usd', 'type' => 'number'],
            /* 19 */ ['identifier' => 'salary_increase_amount', 'path' => 'salary_increase_amount', 'name' => 'Salary Increase Amount', 'type' => 'number'],
            /* 20 */ ['identifier' => 'algo_salary_usd', 'path' => 'algo_salary_usd', 'name' => 'Algo Salary Usd', 'type' => 'number'],
            /* 21 */ ['identifier' => 'new_salary_usd', 'path' => 'new_salary_usd', 'name' => 'New Salary Usd', 'type' => 'number'],
            /* 22 */ ['identifier' => 'salary_increase_amount_usd', 'path' => 'salary_increase_amount_usd', 'name' => 'Salary Increase Amount Usd', 'type' => 'number'],
            /* 23 */ ['identifier' => 'salary_increase_percent', 'path' => 'salary_increase_percent', 'name' => 'Salary Increase Percent', 'type' => 'number'],

            /* 24 */ ['identifier' => 'location', 'path' => null, 'name' => 'Location', 'type' => 'entity:4'],
            /* 25 */ ['identifier' => 'job', 'path' => 'job', 'name' => 'Job', 'type' => 'entity:1'],
            /* 26 */ ['identifier' => 'new_job', 'path' => 'newJob', 'name' => 'New Job', 'type' => 'entity:1'],
            /* 27 */ ['identifier' => 'manager', 'path' => 'manager', 'name' => 'Manager', 'type' => 'entity:2'],
            /* 28 */ ['identifier' => 'subordinates', 'path' => 'subordinates', 'name' => 'Subordinates', 'type' => 'collection:2'],
            /* 29 */ ['identifier' => 'equity', 'path' => null, 'name' => 'Equity', 'type' => 'entity:3'],
        ],
    ],
    3 => [
        'table' => 'employees',
        'name' => 'Equity',
        'fields' => [
            /* 30 */ ['identifier' => 'amount', 'path' => 'equity_amount', 'name' => 'Amount', 'type' => 'number'],
            /* 31 */ ['identifier' => 'rationale', 'path' => 'equity_rationale', 'name' => 'Rationale', 'type' => 'string'],
        ],
    ],
    4 => [
        'table' => 'employees',
        'name' => 'Location',
        'fields' => [
            /* 32 */ ['identifier' => 'name', 'path' => 'location', 'name' => 'name', 'type' => 'string'],
            /* 33 */ ['identifier' => 'region', 'path' => 'region', 'name' => 'region', 'type' => 'string'],
            /* 34 */ ['identifier' => 'country', 'path' => 'country', 'name' => 'country', 'type' => 'string'],
            /* 35 */ ['identifier' => 'country_city', 'path' => 'country_city', 'name' => 'country_city', 'type' => 'string'],
            /* 36 */ ['identifier' => 'city_tier', 'path' => 'city_tier', 'name' => 'city_tier', 'type' => 'string'],
        ],
    ],
];
