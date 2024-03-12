<?php

namespace App\Utils\Format;

enum Format: string
{
    case General = 'General';
    case YesNo = 'YesNo';
    case NumberZeroDecimal = 'NumberZeroDecimal';
    case NumberTwoDecimals = 'NumberTwoDecimals';

    public function label(): string
    {
        return match ($this) {
            Format::General => 'General',
            Format::YesNo => 'Yes/No',
            Format::NumberZeroDecimal => 'Number Zero Decimal',
            Format::NumberTwoDecimals => 'Number Two Decimals',
        };
    }

    public function toExcel(): string
    {
        return match ($this) {
            Format::General => 'General',
            Format::YesNo => '[=0]"No";[=1]"Yes";General',
            Format::NumberZeroDecimal => '#,##0',
            Format::NumberTwoDecimals => '#,##0.00',
        };
    }
}
