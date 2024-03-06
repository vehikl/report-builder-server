<?php

return [
    1 => [
        'table' => 'jobs',
        'name' => 'Job',
        'fields' => [
            /*  1 */ ['identifier' => 'code', 'path' => 'code', 'name' => 'Code', 'type' => 'string'],
            /*  2 */ ['identifier' => 'title', 'path' => 'title', 'name' => 'Title', 'type' => 'string'],
            /*  2 */ ['identifier' => 'ladder', 'path' => 'ladder', 'name' => 'Ladder', 'type' => 'string'],
            /*  2 */ ['identifier' => 'family', 'path' => 'family', 'name' => 'Family', 'type' => 'string'],
            /*  2 */ ['identifier' => 'family_group', 'path' => 'family_group', 'name' => 'Family Group', 'type' => 'string'],
            /*  3 */ ['identifier' => 'employees', 'path' => 'employees', 'name' => 'Employees', 'type' => 'collection:2'],
        ],
    ],
    2 => [
        'table' => 'employees',
        'name' => 'Employee',
        'fields' => [
            /*  4 */ ['identifier' => 'id', 'path' => 'id', 'name' => 'Id', 'type' => 'number'],
            /*  5 */ ['identifier' => 'display_name', 'path' => 'display_name', 'name' => 'Name', 'type' => 'string'],

            /*  5 */ ['identifier' => 'program', 'path' => 'program', 'name' => 'Program', 'type' => 'string'],
            /*  5 */ ['identifier' => 'hire_date', 'path' => 'hire_date', 'name' => 'Hire Date', 'type' => 'string'],
            /*  5 */ ['identifier' => 'is_termed', 'path' => 'is_termed', 'name' => 'Is Termed', 'type' => 'string'],
            /*  5 */ ['identifier' => 'currency_code', 'path' => 'currency_code', 'name' => 'Currency Code', 'type' => 'string'],
            /*  5 */ ['identifier' => 'currency_fx_to_usd', 'path' => 'currency.fx_to_usd', 'name' => 'Currency Fx To Usd', 'type' => 'number'],
            /*  5 */ ['identifier' => 'has_promotion', 'path' => 'has_promotion', 'name' => 'Has Promotion', 'type' => 'boolean'],
            /*  5 */ ['identifier' => 'is_promo_hourly_to_salary', 'path' => 'is_promo_hourly_to_salary', 'name' => 'Is Promo Hourly To Salary', 'type' => 'boolean'],
            /*  5 */ ['identifier' => 'is_promo_salary_to_hourly', 'path' => 'is_promo_salary_to_hourly', 'name' => 'Is Promo Salary To Hourly', 'type' => 'boolean'],
            /*  6 */ ['identifier' => 'salary', 'path' => 'salary', 'name' => 'Salary', 'type' => 'number'],
            /*  6 */ ['identifier' => 'salary_usd', 'path' => 'salary_usd', 'name' => 'Salary Usd', 'type' => 'number'],
            /*  7 */ ['identifier' => 'salary_increase_amount', 'path' => 'salary_increase_amount', 'name' => 'Salary Increase Amount', 'type' => 'number'],
            /*  7 */ ['identifier' => 'algo_salary_usd', 'path' => 'algo_salary_usd', 'name' => 'Algo Salary Usd', 'type' => 'number'],
            /*  7 */ ['identifier' => 'new_salary_usd', 'path' => 'new_salary_usd', 'name' => 'New Salary Usd', 'type' => 'number'],
            /*  7 */ ['identifier' => 'salary_increase_amount_usd', 'path' => 'salary_increase_amount_usd', 'name' => 'Salary Increase Amount Usd', 'type' => 'number'],
            /*  7 */ ['identifier' => 'salary_increase_percent', 'path' => 'salary_increase_percent', 'name' => 'Salary Increase Percent', 'type' => 'number'],

            /* 14 */ ['identifier' => 'location', 'path' => null, 'name' => 'Location', 'type' => 'entity:4'],
            /* 14 */ ['identifier' => 'job', 'path' => 'job', 'name' => 'Job', 'type' => 'entity:1'],
            /* 14 */ ['identifier' => 'new_job', 'path' => 'newJob', 'name' => 'New Job', 'type' => 'entity:1'],
            /* 15 */ ['identifier' => 'manager', 'path' => 'manager', 'name' => 'Manager', 'type' => 'entity:2'],
            /* 16 */ ['identifier' => 'subordinates', 'path' => 'subordinates', 'name' => 'Subordinates', 'type' => 'collection:2'],
            /* 17 */ ['identifier' => 'equity', 'path' => null, 'name' => 'Equity', 'type' => 'entity:3'],

            /*  7 */ ['identifier' => 'bonus', 'path' => 'bonus', 'name' => 'Bonus', 'type' => 'number'],
            /*  8 */ ['identifier' => 'manager_id', 'path' => 'manager_id', 'name' => 'Manager Id', 'type' => 'number'],
            /*  9 */ ['identifier' => 'job_code', 'path' => 'job_code', 'name' => 'Job Code', 'type' => 'string'],
            /* 10 */ ['identifier' => 'total_compensation', 'path' => 'total_compensation', 'name' => 'Total Compensation', 'type' => 'number'],
            /* 11 */ ['identifier' => 'name_with_job', 'path' => 'name_with_job', 'name' => 'Name With Job', 'type' => 'string'],
            /* 12 */ ['identifier' => 'name_with_job_display_name', 'path' => 'name_with_job_display_name', 'name' => 'Name With Job Display Name', 'type' => 'string'],
            /* 13 */ ['identifier' => 'job_title', 'path' => 'job_title', 'name' => 'Job Title', 'type' => 'string'],
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
    4 => [
        'table' => 'employees',
        'name' => 'Location',
        'fields' => [
            /*  5 */ ['identifier' => 'name', 'path' => 'location', 'name' => 'name', 'type' => 'string'],
            /*  5 */ ['identifier' => 'region', 'path' => 'region', 'name' => 'region', 'type' => 'string'],
            /*  5 */ ['identifier' => 'country', 'path' => 'country', 'name' => 'country', 'type' => 'string'],
            /*  5 */ ['identifier' => 'country_city', 'path' => 'country_city', 'name' => 'country_city', 'type' => 'string'],
            /*  5 */ ['identifier' => 'city_tier', 'path' => 'city_tier', 'name' => 'city_tier', 'type' => 'string'],
        ],
    ],
];
