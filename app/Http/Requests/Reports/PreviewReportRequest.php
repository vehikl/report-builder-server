<?php

namespace App\Http\Requests\Reports;

use App\Models\Column;
use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class PreviewReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'entity_id' => ['required', 'exists:entities,id'],
            'columns' => ['required', 'array', 'filled'],
            'columns.*.name' => ['required', 'string'],
            'columns.*.expression' => ['required', 'string']
        ];
    }

    public function columns(): Collection
    {
        return collect($this->input('columns'))
            ->map(fn(array $column) => Column::query()->make($column));
    }

    public function report(): Report
    {
        $report = Report::query()->make();

        return $report->forceFill([
            'name' => $this->input('name'),
            'entity_id' => (int)$this->input('entity_id'),
            'columns' => $this->columns()
                ->map(fn(Column $column) => $column->forceFill(['report' => $report]))
        ]);
    }
}
