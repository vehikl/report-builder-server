<?php

namespace App\Http\Requests\Reports;

use App\Models\Core\Column;
use App\Models\Core\Report;
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
            'columns.*.expression' => ['required', 'array'],
            'sort' => ['sometimes', 'nullable', 'array:key,direction'],
            'sort.key' => ['string'],
            'sort.direction' => ['in:asc,desc'],
        ];
    }

    public function columns(): Collection
    {
        return collect($this->input('columns'))
            ->map(fn (array $column, int $i) => Column::query()->make(['position' => $i, ...$column]));
    }

    public function report(): Report
    {
        $report = Report::query()->make();

        return $report->forceFill([
            'name' => $this->input('name'),
            'entity_id' => $this->input('entity_id'),
            'columns' => $this->columns()
                ->map(fn (Column $column) => $column->forceFill(['report' => $report])),
        ]);
    }
}
